<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialProvider extends Model
{
    protected $fillable = [
        'name',
        'client_id',
        'client_secret',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];
    public function clients()
    {
        return $this->belongsToMany(OauthClient::class, 'client_provider')
            ->withTimestamps();
    }
}
