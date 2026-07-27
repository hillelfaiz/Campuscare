<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_id',
        'nama_fasilitas',
        'main_title',
        'priority_level',
        'report_count',
        'unique_reporter_count',
        'status',
        'first_reported_at',
        'last_reported_at',
    ];

    public function reports()
    {
        return $this->hasMany(FacilityReport::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}