<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PasswordResetRequest extends Model
{
    // protected $table = 'password_reset_requests';
    protected $fillable = ['login','user_id','note','status','handled_at','handled_by'];
}
