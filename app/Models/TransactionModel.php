<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionModel extends Model
{
    protected $connection = 'dp_mysql';
    protected $table = 'transaction';
    public $timestamps = false;
}
