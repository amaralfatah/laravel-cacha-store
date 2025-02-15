<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\StoreBalance;
use App\Models\BalanceMutation;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

trait POSBalanceHandler
{
    protected function getDefaultStore()
    {
        return Store::where('code', 'MAIN')->first() ?? Store::create([
            'code' => 'MAIN',
            'name' => 'Toko Cacha',
            'address' => 'Emplak, Kalipucang, Pangandaran',
            'phone' => '081234567890',
            'email' => 'tokocacha@example.com',
            'is_active' => true
        ]);
    }

    protected function handleTransactionBalance($transaction)
    {
        try {
            DB::beginTransaction();

            // Get default store
            $store = $transaction->store_id ?
                Store::findOrFail($transaction->store_id) :
                $this->getDefaultStore();

            // Get or create store balance
            $storeBalance = StoreBalance::firstOrCreate(
                [],
                [
                    'cash_amount' => 0,
                    'non_cash_amount' => 0,
                    'last_updated_by' => auth()->id()
                ]
            );

            $isDefaultPayment = $transaction->payment_type === 'cash';
            $balanceField = $isDefaultPayment ? 'cash_amount' : 'non_cash_amount';
            $previousBalance = $storeBalance->$balanceField;
            $newBalance = $previousBalance + $transaction->final_amount;

            $storeBalance->$balanceField = $newBalance;
            $storeBalance->last_updated_by = auth()->id();
            $storeBalance->save();

            BalanceMutation::create([
                'store_id' => $store->id,
                'type' => 'in',
                'payment_method' => $transaction->payment_type,
                'amount' => $transaction->final_amount,
                'previous_balance' => $previousBalance,
                'current_balance' => $newBalance,
                'source_type' => get_class($transaction),
                'source_id' => $transaction->id,
                'notes' => "Payment for invoice {$transaction->invoice_number}",
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    protected function revertTransactionBalance($transaction)
    {
        try {
            DB::beginTransaction();

            // Get default store
            $store = $transaction->store_id ?
                Store::findOrFail($transaction->store_id) :
                $this->getDefaultStore();

            $storeBalance = StoreBalance::firstOrFail();
            $isDefaultPayment = $transaction->payment_type === 'cash';
            $balanceField = $isDefaultPayment ? 'cash_amount' : 'non_cash_amount';
            $currentBalance = $storeBalance->$balanceField;

            if ($currentBalance < $transaction->final_amount) {
                throw new \Exception(
                    'Insufficient ' .
                    ($isDefaultPayment ? 'cash' : 'non-cash') .
                    ' balance for reversal'
                );
            }

            $newBalance = $currentBalance - $transaction->final_amount;
            $storeBalance->$balanceField = $newBalance;
            $storeBalance->last_updated_by = auth()->id();
            $storeBalance->save();

            BalanceMutation::create([
                'store_id' => $store->id,
                'type' => 'out',
                'payment_method' => $transaction->payment_type,
                'amount' => $transaction->final_amount,
                'previous_balance' => $currentBalance,
                'current_balance' => $newBalance,
                'source_type' => get_class($transaction),
                'source_id' => $transaction->id,
                'notes' => "Reversal for invoice {$transaction->invoice_number}",
                'created_by' => auth()->id()
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }
}
