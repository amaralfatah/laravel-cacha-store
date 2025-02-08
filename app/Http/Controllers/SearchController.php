<?php
// app/Http/Controllers/SearchController.php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Transaction;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->get('q');

        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'like', "%{$search}%")
            ->orWhere('barcode', 'like', "%{$search}%")
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'type' => 'product',
                    'id' => $product->id,
                    'title' => $product->name,
                    'subtitle' => 'Barcode: ' . $product->barcode,
                    'url' => route('products.show', $product->id)
                ];
            });

        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->take(5)
            ->get()
            ->map(function ($customer) {
                return [
                    'type' => 'customer',
                    'id' => $customer->id,
                    'title' => $customer->name,
                    'subtitle' => $customer->phone,
                    'url' => route('customers.show', $customer->id)
                ];
            });

        $suppliers = Supplier::where('name', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->take(5)
            ->get()
            ->map(function ($supplier) {
                return [
                    'type' => 'supplier',
                    'id' => $supplier->id,
                    'title' => $supplier->name,
                    'subtitle' => $supplier->phone,
                    'url' => route('suppliers.show', $supplier->id)
                ];
            });

        $transactions = Transaction::where('invoice_number', 'like', "%{$search}%")
            ->take(5)
            ->get()
            ->map(function ($transaction) {
                return [
                    'type' => 'transaction',
                    'id' => $transaction->id,
                    'title' => 'Invoice #' . $transaction->invoice_number,
                    'subtitle' => $transaction->created_at->format('d M Y'),
                    'url' => route('transactions.show', $transaction->id)
                ];
            });

        $results = [
            'products' => $products,
            'customers' => $customers,
            'suppliers' => $suppliers,
            'transactions' => $transactions
        ];

        return response()->json($results);
    }
}
