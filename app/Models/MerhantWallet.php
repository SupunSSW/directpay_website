<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerhantWallet extends Model
{
    protected $connection = 'dp_mysql';
    protected $table = 'merchant_wallet';
    public $timestamps = false;
}
