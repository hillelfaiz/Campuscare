<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_ruangan',
        'gedung',
        'lantai',
    ];

    public function reports()
    {
        return $this->hasMany(FacilityReport::class);
    }
}