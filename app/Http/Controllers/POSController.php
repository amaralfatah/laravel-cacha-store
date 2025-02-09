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

            // Modifikasi validasi untuk invoice number
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

            $total_amount = 0;
            $total_tax = 0;
            $total_discount = 0;

            // Hitung total
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

            if ($request->status === 'pending') {
                if ($request->pending_transaction_id) {
                    // Update existing pending transaction
                    $transaction = Transaction::findOrFail($request->pending_transaction_id);

                    // Delete old items
                    $transaction->items()->delete();

                    // Update transaction details
                    $transaction->update([
                        'customer_id' => $request->customer_id,
                        'total_amount' => $total_amount,
                        'tax_amount' => $total_tax,
                        'discount_amount' => $total_discount,
                        'final_amount' => $total_amount + $total_tax - $total_discount,
                        'payment_type' => $request->payment_type,
                        'reference_number' => $request->reference_number,
                        'invoice_date' => now()
                        // Tidak update invoice_number dan status karena sudah ada
                    ]);
                } else {
                    // Create new pending transaction
                    $transaction = Transaction::create([
                        'invoice_number' => $request->invoice_number,
                        'customer_id' => $request->customer_id,
                        'cashier_id' => Auth::id(),
                        'total_amount' => $total_amount,
                        'tax_amount' => $total_tax,
                        'discount_amount' => $total_discount,
                        'final_amount' => $total_amount + $total_tax - $total_discount,
                        'payment_type' => $request->payment_type,
                        'reference_number' => $request->reference_number,
                        'status' => 'pending',
                        'invoice_date' => now()
                    ]);
                }
            } else {
                // Create new completed transaction
                $transaction = Transaction::create([
                    'invoice_number' => $request->invoice_number,
                    'customer_id' => $request->customer_id,
                    'cashier_id' => Auth::id(),
                    'total_amount' => $total_amount,
                    'tax_amount' => $total_tax,
                    'discount_amount' => $total_discount,
                    'final_amount' => $total_amount + $total_tax - $total_discount,
                    'payment_type' => $request->payment_type,
                    'reference_number' => $request->reference_number,
                    'status' => 'success',
                    'invoice_date' => now()
                ]);

                // If this was from a pending transaction, delete it
                if ($request->pending_transaction_id) {
                    $pendingTransaction = Transaction::find($request->pending_transaction_id);
                    if ($pendingTransaction) {
                        $pendingTransaction->items()->delete();
                        $pendingTransaction->delete();
                    }
                }
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
                if ($request->status !== 'pending') {
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

        if (!$latestInvoice) {
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
