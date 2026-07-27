<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CampusAsset extends Model
{
    use HasFactory;

    protected $table = 'campus_assets';

    protected $fillable = [
        'kode',
        'nama',
        'lokasi',
        'kategori',
        'deskripsi',
    ];
}