<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class emailNotifications extends Model
{
    const EMAIL500K = "500K_EMAIL_NOTIFICATION";
    const EMAIL300K = "300K_EMAIL_NOTIFICATION";
    const EMAIL600k = "600K_EMAIL_NOTIFICATION";
    const EMAIL1000k = "1000K_EMAIL_NOTIFICATION";



    protected $table = 'emailNotification';
}
