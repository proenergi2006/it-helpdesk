<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetMutation extends Model
{
    protected $fillable = [
        'asset_id', 'mutated_at', 'from_cabang', 'from_location', 'from_user',
        'to_cabang', 'to_location', 'to_user', 'reason', 'created_by',
    ];

    protected $casts = ['mutated_at' => 'date'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
