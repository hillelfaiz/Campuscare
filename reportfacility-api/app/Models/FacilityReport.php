<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_group_id',
        'room_id',
        'reporter_id',
        'nama_fasilitas',
        'deskripsi_kerusakan',
        'foto',
        'status',
        'catatan_teknisi',
        'assigned_to',
        'similarity_score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function reportGroup()
    {
        return $this->belongsTo(ReportGroup::class, 'report_group_id');
    }
}