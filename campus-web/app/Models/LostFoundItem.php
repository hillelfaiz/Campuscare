<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LostFoundItem extends Model
{
    protected $fillable = [
        'type', 'name', 'category', 'location', 'description', 'photo', 
        'status', 'reporter_id_photo', 'reported_by', 
        'claim_name', 'claim_id_type', 'claim_id_number',
        'claim_proof_photo', 'claim_handover_photo', 'claim_history',
        'claimed_by', 'claimed_at', 'scheduled_at'
    ];
    
    protected $casts = [
        'claimed_at' => 'datetime', 
        'scheduled_at' => 'datetime',
        'claim_history' => 'array',
    ];

    public function getPhotoAttribute($value) {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return asset('storage/' . $value);
    }
    
    public function getClaimProofPhotoAttribute($value) {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return asset('storage/' . $value);
    }

    public function getClaimHandoverPhotoAttribute($value) {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return asset('storage/' . $value);
    }

    public function getReporterIdPhotoAttribute($value) {
        if (!$value) return null;
        if (str_starts_with($value, 'http')) return $value;
        return asset('storage/' . $value);
    }

    public function reporter() { return $this->belongsTo(User::class, 'reported_by'); }
    public function verifier() { return $this->belongsTo(User::class, 'claimed_by'); }
}