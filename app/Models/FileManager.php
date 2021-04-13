<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileManager extends Model
{
    const UPLOAD = 1;
    const DOWNLOAD = 2;

    protected $table = 'file_manager';
}
