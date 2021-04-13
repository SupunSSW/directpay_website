<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardTokenModel extends Model
{
    protected $connection = 'dp_mysql';
    protected $table = 'card_tokens';
    public $timestamps = false;
}
