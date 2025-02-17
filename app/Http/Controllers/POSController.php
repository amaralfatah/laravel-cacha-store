<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\ProductUnit;
use App\Models\Store;
use App\Models\Transaction;
use App\Traits\StockMovementHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class POSController extends Controller
{

    use POSBalanceHandler, StockMovementHandler;
    public function index()
    {
        $customers = Customer::all();
        $latestInvoice = Transaction::latest()->first();
        $invoiceNumber = $this->generateInvoiceNumber($latestInvoice);
        $cartData = session('cart_data');

        // Add store handling
        $stores = [];
        if (Auth::user()->role === 'admin') {
            $stores = Store::all();
            $selectedStore = session('selected_store_id') ? Store::find(session('selected_store_id')) : null;
        } else {
            $selectedStore = Auth::user()->store;
        }

        return view('pos.index', compact('customers', 'invoiceNumber', 'cartData', 'stores', 'selectedStore'));
    }

    public function getProduct(Request $request)
    {
        try {
            $product = Product::with([
                'tax',
                'discount',
                'productUnits.unit',
                'productUnits.prices'
            ])
                ->where('barcode', $request->barcode)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            // Check for default unit
            $defaultUnit = $product->productUnits->where('is_default', true)->first();
            if (!$defaultUnit) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak memiliki unit default'
                ], 400);
            }

            $availableUnits = $product->productUnits->map(function ($productUnit) {
                return [
                    'unit_id' => $productUnit->unit_id,
                    'unit_name' => $productUnit->unit->name,
                    'conversion_factor' => $productUnit->conversion_factor,
                    'selling_price' => $productUnit->selling_price,
                    'stock' => $productUnit->stock,
                    'is_default' => $productUnit->is_default,
                    'prices' => $productUnit->prices->map(function ($price) {
                        return [
                            'min_quantity' => $price->min_quantity,
                            'price' => $price->price
                        ];
                    })
                ];
            });

            return response()->json([
                'success' => true,
                'data' => array_merge($product->toArray(), [
                    'available_units' => $availableUnits
                ])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchProduct(Request $request)
    {
        try {
            $search = $request->search;

            $products = Product::where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('barcode', 'like', "%{$search}%");
            })
                ->where('is_active', true)
                ->with([
                    'productUnits.unit',
                    'productUnits.prices',
                    'tax',
                    'discount'
                ])
                ->limit(10)
                ->get();

            $formattedProducts = $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'code' => $product->code,
                    'barcode' => $product->barcode,
                    'description' => $product->description,
                    'short_description' => $product->short_description,
                    'available_units' => $product->productUnits->map(function ($pu) {
                        return [
                            'product_unit_id' => $pu->id,
                            'unit_id' => $pu->unit_id,
                            'unit_name' => $pu->unit->name,
                            'unit_code' => $pu->unit->code,
                            'selling_price' => $pu->selling_price,
                            'stock' => $pu->stock,
                            'min_stock' => $pu->min_stock,
                            'is_default' => $pu->is_default,
                            'conversion_factor' => $pu->conversion_factor,
                            'prices' => $pu->prices->map(function ($price) {
                                return [
                                    'min_quantity' => $price->min_quantity,
                                    'price' => $price->price
                                ];
                            })
                        ];
                    }),
                    'tax' => $product->tax ? [
                        'id' => $product->tax->id,
                        'name' => $product->tax->name,
                        'rate' => $product->tax->rate
                    ] : null,
                    'discount' => $product->discount ? [
                        'id' => $product->discount->id,
                        'name' => $product->discount->name,
                        'type' => $product->discount->type,
                        'value' => $product->discount->value
                    ] : null
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedProducts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $validated = $request->validate([
                'invoice_number' => [
                    'required',
                    'string',
                    Rule::unique('transactions')->ignore($request->pending_transaction_id)
                ],
                'customer_id' => 'required|exists:customers,id',
                'store_id' => 'required|exists:stores,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'payment_type' => 'required|in:cash,transfer',
                'reference_number' => 'required_if:payment_type,transfer',
                'pending_transaction_id' => 'nullable|exists:transactions,id'
            ]);

            // Verify store access
            if (Auth::user()->role !== 'admin' && Auth::user()->store_id !== (int)$request->store_id) {
                throw new \Exception('Unauthorized store access');
            }

            // Calculate totals
            $total_amount = 0;
            $total_tax = 0;
            $total_discount = 0;

            // Validate stock availability first
            if ($request->status === 'success') {
                $this->validateStockAvailability($request->items);
            }

            foreach ($request->items as $item) {
                $product = Product::with(['productUnits', 'tax', 'discount'])->find($item['product_id']);

                $unit_price = $product->getPrice($item['quantity'], $item['unit_id']);
                $subtotal = $unit_price * $item['quantity'];
                $discount = $product->getDiscountAmount($unit_price) * $item['quantity'];
                $tax = $product->getTaxAmount($subtotal - $discount);

                $total_amount += $subtotal;
                $total_tax += $tax;
                $total_discount += $discount;
            }

            $transactionData = [
                'store_id' => $request->store_id,
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'cashier_id' => Auth::id(),
                'total_amount' => $total_amount,
                'tax_amount' => $total_tax,
                'discount_amount' => $total_discount,
                'final_amount' => $total_amount + $total_tax - $total_discount,
                'payment_type' => $request->payment_type,
                'reference_number' => $request->reference_number,
                'status' => $request->status,
                'invoice_date' => now()
            ];

            // Create or update transaction
            if ($request->pending_transaction_id) {
                $transaction = Transaction::findOrFail($request->pending_transaction_id);
                $transaction->items()->delete();
                $transaction->update($transactionData);
            } else {
                $transaction = Transaction::create($transactionData);
            }

            // Create transaction items and handle stock movements
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unit_price = $product->getPrice($item['quantity'], $item['unit_id']);
                $discount = $product->getDiscountAmount($unit_price);
                $subtotal = $unit_price * $item['quantity'];

                $transactionItem = $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unit_price,
                    'subtotal' => $subtotal,
                    'discount' => $discount
                ]);

                // Handle stock movement only for completed transactions
                if ($transaction->status === 'success') {
                    $productUnit = ProductUnit::where('product_id', $item['product_id'])
                        ->where('unit_id', $item['unit_id'])
                        ->first();

                    if ($productUnit) {
                        $this->handleStockMovement(
                            $productUnit,
                            $item['quantity'],
                            'out',
                            'transaction_items',
                            $transactionItem->id,
                            "Transaction sale: {$transaction->invoice_number}"
                        );
                    }
                }
            }

            // Handle balance only for successful transactions
            if ($transaction->status === 'success') {
                $this->handleTransactionBalance($transaction);
            }

            DB::commit();

            // Clear session data if transaction is completed
            if ($transaction->status === 'success') {
                session()->forget('cart_data');
            }

            return response()->json([
                'success' => true,
                'transaction_id' => $transaction->id,
                'message' => $request->status === 'pending' ?
                    'Transaksi berhasil disimpan sebagai draft' :
                    'Transaksi berhasil disimpan'
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateInvoiceNumber($latestInvoice)
    {
        $prefix = 'INV' . date('Ymd');

        // If no latest invoice exists, start with 0001
        if (!$latestInvoice) {
            return $prefix . '0001';
        }

        // If latest invoice is from a different date, start with 0001
        if (!str_starts_with($latestInvoice->invoice_number, $prefix)) {
            return $prefix . '0001';
        }

        $lastNumber = substr($latestInvoice->invoice_number, -4);
        $nextNumber = str_pad((int)$lastNumber + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextNumber;
    }

    public function printInvoice(Transaction $transaction)
    {
        // Load relations yang diperlukan
        $transaction->load([
            'customer',
            'user',
            'items.product',
            'items.unit'
        ]);

        // Format data untuk invoice
        $data = [
            'transaction' => $transaction,
            'company' => [
                'name' => 'Toko Cacha',
                'address' => 'Emplak, Kalipucang, Pangandaran',
                'phone' => 'Telp: 081234567890'
            ]
        ];

        return view('pos.invoice', $data);
    }

    public function clearPending(Request $request)
    {
        try {
            $transactionId = $request->input('transaction_id');

            if ($transactionId) {
                // Find the pending transaction
                $transaction = Transaction::where('id', $transactionId)
                    ->where('status', 'pending')
                    ->first();

                if ($transaction) {
                    // Check user authorization
                    if (auth()->user()->role !== 'admin' &&
                        $transaction->store_id !== auth()->user()->store_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Unauthorized to clear this transaction'
                        ], 403);
                    }

                    // Delete transaction items first
                    $transaction->items()->delete();
                    // Then delete the transaction
                    $transaction->delete();
                }
            }

            // Clear the cart data from session
            session()->forget('cart_data');

            return response()->json([
                'success' => true,
                'message' => 'Pending transaction cleared successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error clearing pending transaction: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear pending transaction: ' . $e->getMessage()
            ]);
        }
    }

}
