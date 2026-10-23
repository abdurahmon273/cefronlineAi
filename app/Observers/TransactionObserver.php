<?php

namespace App\Observers;



use App\Models\CustomTransaction;
use Goodoneuz\PayUz\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "created" event.
     */
    public function created(Transaction $transaction): void
    {
        $user =$transaction->transactionable()->first();

        if ($user) {
            $transaction=CustomTransaction::find($transaction->id);
            $transaction->update([
                'tmp_payment_slug'=>$user->tmp_payment_slug,
                'tmp_payment_price'=>$user->tmp_payment_price,
                'tmp_payment_promocode'=>$user->tmp_payment_promocode,
                'tmp_payment_type'=>$user->tmp_payment_type,
            ]);
        }

    }

    /**
     * Handle the Transaction "updated" event.
     */
    public function updated(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "deleted" event.
     */
    public function deleted(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "restored" event.
     */
    public function restored(Transaction $transaction): void
    {
        //
    }

    /**
     * Handle the Transaction "force deleted" event.
     */
    public function forceDeleted(Transaction $transaction): void
    {
        //
    }
}
