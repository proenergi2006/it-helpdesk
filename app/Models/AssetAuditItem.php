<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAuditItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['asset_audit_id', 'asset_id', 'scanned_by', 'scanned_at', 'note'];

    protected $casts = ['scanned_at' => 'datetime'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }
}
