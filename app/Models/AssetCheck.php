<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Hasil cek kondisi aset saat QR dipindai (untuk audit)
class AssetCheck extends Model
{
    public const CONDITIONS = [
        'good'        => ['label' => 'Baik', 'badge' => 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'poor'        => ['label' => 'Buruk', 'badge' => 'bg-red-50 text-red-700 ring-red-200'],
        'need_repair' => ['label' => 'Perlu perbaikan', 'badge' => 'bg-amber-50 text-amber-700 ring-amber-200'],
    ];

    protected $fillable = ['asset_id', 'condition', 'cabang', 'room', 'location_mismatch', 'note', 'checked_by'];

    protected $casts = ['location_mismatch' => 'boolean'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function getConditionLabelAttribute(): string
    {
        return self::CONDITIONS[$this->condition]['label'] ?? $this->condition;
    }

    public function getConditionBadgeAttribute(): string
    {
        return self::CONDITIONS[$this->condition]['badge'] ?? 'bg-gray-50 text-gray-600 ring-gray-200';
    }
}
