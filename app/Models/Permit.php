<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    use HasFactory;

    // Jika nama tabel di database Anda bukan 'permits' (misal: 'permit' tanpa 's'), 
    // aktifkan baris di bawah ini dengan menghapus tanda //
    // protected $table = 'permits';

    // Jika Anda tidak menggunakan kolom timestamps (created_at & updated_at), 
    // set menjadi false agar tidak error saat insert data
    public $timestamps = true;
}