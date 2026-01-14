<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OauthClient extends Model
{
    protected $fillable = [
        'client_id',
        'client_secret',
        'name',
        'redirect_uris',
        'allowed_scopes',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'redirect_uris' => 'array',
        'allowed_scopes' => 'array',
    ];
}
