<?php

namespace App\Http\Controllers;

use App\Models\StoreBalance;
use App\Models\BalanceMutation;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

trait POSBalanceHandler
{
    protected function handleTransactionBalance($transaction)
    {
        try {
            DB::beginTransaction();

            // Get or create store balance
            $storeBalance = StoreBalance::firstOrCreate(
                [],
                [
                    'cash_amount' => 0,
                    'non_cash_amount' => 0,
                    'last_updated_by' => auth()->id()
                ]
            );

            // Determine payment method and previous balance
            $isDefaultPayment = $transaction->payment_type === 'cash';
            $balanceField = $isDefaultPayment ? 'cash_amount' : 'non_cash_amount';
            $previousBalance = $storeBalance->$balanceField;

            // Calculate new balance
            $newBalance = $previousBalance + $transaction->final_amount;

            // Update store balance
            $storeBalance->$balanceField = $newBalance;
            $storeBalance->last_updated_by = auth()->id();
            $storeBalance->save();

            // Create balance mutation record
            BalanceMutation::create([
                'store_id' => $transaction->store_id ?? 1, // Assuming default store if not set
                'type' => 'in',
                'payment_method' => $transaction->payment_type,
                'amount' => $transaction->final_amount,
                'previous_balance' => $previousBalance,
                'current_balance' => $newBalance,
                'source_type' => Transaction::class,
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

            $storeBalance = StoreBalance::firstOrFail();

            // Determine payment method and current balance
            $isDefaultPayment = $transaction->payment_type === 'cash';
            $balanceField = $isDefaultPayment ? 'cash_amount' : 'non_cash_amount';
            $currentBalance = $storeBalance->$balanceField;

            // Check if sufficient balance exists
            if ($currentBalance < $transaction->final_amount) {
                throw new \Exception(
                    'Insufficient ' .
                    ($isDefaultPayment ? 'cash' : 'non-cash') .
                    ' balance for reversal'
                );
            }

            // Calculate new balance
            $newBalance = $currentBalance - $transaction->final_amount;

            // Update store balance
            $storeBalance->$balanceField = $newBalance;
            $storeBalance->last_updated_by = auth()->id();
            $storeBalance->save();

            // Create balance mutation record for reversal
            BalanceMutation::create([
                'store_id' => $transaction->store_id ?? 1, // Assuming default store if not set
                'type' => 'out',
                'payment_method' => $transaction->payment_type,
                'amount' => $transaction->final_amount,
                'previous_balance' => $currentBalance,
                'current_balance' => $newBalance,
                'source_type' => Transaction::class,
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
