<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Iuran extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_iuran',
        'total_bayar',
        'status',
        'tgl_bayar',
    ];

    // Tentukan primary key tabel
    protected $primaryKey = 'id_bayar';

    // Jika primary key bukan auto-increment integer, tambahkan ini
    public $incrementing = true;
    protected $keyType = 'int';

    // Relasi ke model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}