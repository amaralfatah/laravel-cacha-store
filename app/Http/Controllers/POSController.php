<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class POSController extends Controller
{
    public function index()
    {
        $customers = Customer::all();
        $latestInvoice = Transaction::latest()->first();
        $invoiceNumber = $this->generateInvoiceNumber($latestInvoice);

        // Check if there's cart data from a pending transaction
        $cartData = session('cart_data');

        return view('pos.index', compact('customers', 'invoiceNumber', 'cartData'));
    }

    public function getProduct(Request $request)
    {
        $product = Product::with([
            'defaultUnit',
            'tax',
            'discount',
            'productUnits.unit',
            'priceTiers' => function ($query) {
                $query->orderBy('min_quantity', 'desc');
            }
        ])
            ->where('barcode', $request->barcode)
            ->where('is_active', true)
            ->firstOrFail();

        $response = $product->toArray();
        $response['available_units'] = $product->productUnits->map(function ($pu) {
            return [
                'unit_id' => $pu->unit_id,
                'unit_name' => $pu->unit->name,
                'conversion_factor' => $pu->conversion_factor,
                'price' => $pu->price
            ];
        });

        return response()->json($response);
    }

    public function searchProduct(Request $request)
    {
        $search = $request->search;

        $products = Product::where(function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('barcode', 'like', "%{$search}%");
        })
            ->where('is_active', true)
            ->with(['defaultUnit', 'tax', 'discount'])
            ->limit(10)
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Basic validation
            $validated = $request->validate([
                'invoice_number' => [
                    'required',
                    'string',
                    Rule::unique('transactions')->ignore($request->pending_transaction_id)
                ],
                'customer_id' => 'required|exists:customers,id',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.unit_id' => 'required|exists:units,id',
                'items.*.quantity' => 'required|numeric|min:0.01',
                'payment_type' => 'required|in:cash,transfer',
                'reference_number' => 'required_if:payment_type,transfer',
                'pending_transaction_id' => 'nullable|exists:transactions,id'
            ]);

            // Calculate totals
            $total_amount = 0;
            $total_tax = 0;
            $total_discount = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unit_price = $product->getPrice($item['quantity'], $item['unit_id']);
                $subtotal = $unit_price * $item['quantity'];
                $discount = $product->getDiscountAmount($unit_price) * $item['quantity'];
                $tax = $product->getTaxAmount($subtotal - $discount);

                $total_amount += $subtotal;
                $total_tax += $tax;
                $total_discount += $discount;
            }

            $transactionData = [
                'customer_id' => $request->customer_id,
                'total_amount' => $total_amount,
                'tax_amount' => $total_tax,
                'discount_amount' => $total_discount,
                'final_amount' => $total_amount + $total_tax - $total_discount,
                'payment_type' => $request->payment_type,
                'reference_number' => $request->reference_number,
                'invoice_date' => now()
            ];

            if ($request->pending_transaction_id) {
                // Update existing transaction
                $transaction = Transaction::findOrFail($request->pending_transaction_id);

                // Delete existing items
                $transaction->items()->delete();

                if ($request->status === 'success') {
                    $transactionData['status'] = 'success';
                }

                $transaction->update($transactionData);
            } else {
                // Create new transaction
                $transactionData['invoice_number'] = $request->invoice_number;
                $transactionData['cashier_id'] = Auth::id();
                $transactionData['status'] = $request->status;

                $transaction = Transaction::create($transactionData);
            }

            // Create transaction items
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $unit_price = $product->getPrice($item['quantity'], $item['unit_id']);
                $discount = $product->getDiscountAmount($unit_price);
                $subtotal = $unit_price * $item['quantity'];

                $transaction->items()->create([
                    'product_id' => $item['product_id'],
                    'unit_id' => $item['unit_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unit_price,
                    'subtotal' => $subtotal,
                    'discount' => $discount
                ]);

                // Update inventory only for completed transactions
                if ($transaction->status === 'success') {
                    $inventory = Inventory::where('product_id', $item['product_id'])
                        ->where('unit_id', $item['unit_id'])
                        ->first();

                    if ($inventory) {
                        if ($inventory->quantity < $item['quantity']) {
                            throw new \Exception('Stok tidak mencukupi untuk produk ' . $product->name);
                        }
                        $inventory->decrement('quantity', $item['quantity']);
                    }
                }
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
            'cashier',
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
}
