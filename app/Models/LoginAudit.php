<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAudit extends Model
{
    protected $fillable = [
        'user_id',
        'client_id',
        'ip_address',
        'user_agent',
        'status',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
