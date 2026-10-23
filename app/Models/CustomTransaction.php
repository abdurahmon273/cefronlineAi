<?php

namespace App\Models;

use Goodoneuz\PayUz\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomTransaction extends Transaction
{
    use HasFactory;

    protected $table = 'transactions';

    protected $fillable = [
        'payment_system', //varchar 191
        'system_transaction_id', // varchar 191
        'amount', // double (15,5)
        'currency_code', // int(11)
        'state', // int(11)
        'updated_time', //datetime
        'comment', // varchar 191
        'transactionable_type',
        'transactionable_id',
        'detail', // details
        'tmp_payment_slug',
        'tmp_payment_price',
        'tmp_payment_promocode',
        'tmp_payment_type',
    ];
}
