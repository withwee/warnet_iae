<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Events\NotificationSent;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'read', 'read_at'];

    protected $casts = [
        'read' => 'boolean',
        'read_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function ($notification) {
            broadcast(new NotificationSent($notification))->toOthers();
        });
    }

    /**
     * Get the user that owns the notification.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
