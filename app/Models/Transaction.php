<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    const TYPE_QR = 'QR';
    const TYPE_LINK = 'PAYMENT_LINK';
    const TYPE_CASH = 'CASH';

    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_ERROR = 'ERROR';

    protected $table = 'transactions';
}
