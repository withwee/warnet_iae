<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    // Di Model User
protected $fillable = [
    'name', 
    'email', 
    'password', 
    'nik', 
    'no_kk', 
    'phone', 
    'role', 
    'jumlah_LK', 
    'jumlah_PR', 
    'photo' // Tambahkan ini
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function iurans()
{
    return $this->hasMany(Iuran::class, 'no_kk', 'no_kk');
}

}