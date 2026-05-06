<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockedUser extends Model
{
    protected $fillable = ['name', 'first_name', 'last_name', 'email', 'phone', 'dni', 'reason'];
}
