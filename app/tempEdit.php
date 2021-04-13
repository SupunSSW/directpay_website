<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tempEdit extends Model
{
    const TYPE_PENDING = 0;
    const TYPE_REJECT = 1;
    const TPYE_DELETE = 2;
}
