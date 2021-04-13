<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    const CREATE = 0;
    const EXPIRE = 1;
    const OPEN = 2;
    const SUCCESS = 3;
    const FAILED = 4;
    const SMS_FAILED = 5;
    const POLICY_VALIDATE_CLICK = 6;
    const POLICY_VALIDATE_SUCCESS = 7;
    const POLICY_VALIDATE_FAILED = 8;
    const CONCENT_CLICK = 9;
}
