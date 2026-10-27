<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMutation extends Model
{
    protected $fillable = [
        'asset_id', 'mutated_at', 'from_cabang', 'from_location', 'from_user',
        'to_cabang', 'to_location', 'to_user', 'reason', 'created_by',
        'photo_path', 'receipt_path', 'from_user_department', 'to_user_department',
    ];

    protected $casts = ['mutated_at' => 'date'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
