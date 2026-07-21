<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = ['cliente_id', 'repartidor_id', 'endpoint', 'endpoint_hash', 'public_key', 'auth_token', 'content_encoding', 'user_agent', 'last_used_at'];
    protected function casts(): array { return ['last_used_at' => 'datetime']; }
}
