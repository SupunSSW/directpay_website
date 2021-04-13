<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchedulModel extends Model
{
    //scheduled_payments
    protected $connection = 'dp_mysql';
    protected $table = 'scheduled_payments';
    public $timestamps = false;
}
