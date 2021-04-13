<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class update_link extends Model
{
    const CREATE = 0;
    const EXPIRE = 1;
    const OPEN = 2;
    const SUCCESS= 3;
    const FAILED = 4;
    const SMS_FAILED = 5;

    protected $table = 'update_link';
}
