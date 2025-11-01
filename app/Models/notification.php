<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'read', 'read_at'];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];
}
