<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BalanceMutation;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StoreBalanceController extends Controller
{
    public function adjustment(Request $request, Store $store)
    {
        $validated = $request->validate([
            'type' => 'required|in:cash,transfer',
            'adjustment_type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'required|string|min:3'
        ], [
            'amount.min' => 'The amount must be greater than 0.',
            'notes.min' => 'The notes must be at least 3 characters.'
        ]);


        // Check if store balance exists
        $storeBalance = $store->storeBalance;
        if (!$storeBalance) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Store balance not initialized');
        }

        // Check if the balance will be negative after adjustment
        $currentBalance = $request->type === 'cash' ?
            $storeBalance->cash_amount :
            $storeBalance->non_cash_amount;

        if ($request->adjustment_type === 'out' && $currentBalance < $request->amount) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Insufficient balance for this adjustment');
        }

        DB::beginTransaction();
        try {
            $previousBalance = $request->type === 'cash' ?
                $storeBalance->cash_amount :
                $storeBalance->non_cash_amount;

            // Update store balance
            if ($request->type === 'cash') {
                $storeBalance->cash_amount += $request->adjustment_type === 'in' ?
                    $request->amount :
                    -$request->amount;
            } else {
                $storeBalance->non_cash_amount += $request->adjustment_type === 'in' ?
                    $request->amount :
                    -$request->amount;
            }

            $storeBalance->last_updated_by = Auth::id();
            $storeBalance->save();

            // Create balance mutation record
            BalanceMutation::create([
                'store_id' => $store->id,
                'type' => $request->adjustment_type,
                'payment_method' => $request->type,
                'amount' => $request->amount,
                'previous_balance' => $previousBalance,
                'current_balance' => $request->type === 'cash' ?
                    $storeBalance->cash_amount :
                    $storeBalance->non_cash_amount,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'source_type' => 'App\\Models\\StoreBalance', // Add the model namespace
                'source_id' => $storeBalance->id             // Add the source_id
            ]);

            DB::commit();
            return redirect()->back()->with('success', 'Balance adjusted successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to adjust balance: ' . $e->getMessage());
        }
    }

    public function history(Request $request, Store $store)
    {

        if ($request->ajax()) {
            $query = BalanceMutation::with('createdBy')
                ->where('store_id', $store->id);

            return datatables()->of($query)
                ->addColumn('date', function($mutation) {
                    return $mutation->created_at->format('Y-m-d H:i');
                })
                ->addColumn('type_badge', function($mutation) {
                    return $mutation->type === 'in' ?
                        '<span class="badge bg-success">IN</span>' :
                        '<span class="badge bg-danger">OUT</span>';
                })
                ->addColumn('amount_formatted', function($mutation) {
                    return number_format($mutation->amount, 2);
                })
                ->addColumn('balance_formatted', function($mutation) {
                    return number_format($mutation->current_balance, 2);
                })
                ->rawColumns(['type_badge'])
                ->make(true);
        }

        return view('store-balance.history', compact('store'));
    }

    public function show(Store $store)
    {
        $balance = $store->storeBalance;

        $todayMutations = BalanceMutation::where('store_id', $store->id)
            ->whereDate('created_at', today())
            ->selectRaw('
            payment_method,
            type,
            COUNT(*) as count,
            SUM(amount) as total_amount
        ')
            ->groupBy('payment_method', 'type')
            ->get();

        $summary = [
            'cash_balance' => $balance->cash_amount,
            'non_cash_balance' => $balance->non_cash_amount,
            'total_balance' => $balance->cash_amount + $balance->non_cash_amount,
            'today_cash_in' => $todayMutations
                    ->where('payment_method', 'cash')
                    ->where('type', 'in')
                    ->first()?->total_amount ?? 0,
            'today_cash_out' => $todayMutations
                    ->where('payment_method', 'cash')
                    ->where('type', 'out')
                    ->first()?->total_amount ?? 0,
            'today_transfer_in' => $todayMutations
                    ->where('payment_method', 'transfer')
                    ->where('type', 'in')
                    ->first()?->total_amount ?? 0,
            'today_transfer_out' => $todayMutations
                    ->where('payment_method', 'transfer')
                    ->where('type', 'out')
                    ->first()?->total_amount ?? 0,
        ];

        return view('store-balance.show', compact('summary', 'store'));
    }

}
