<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRepairPhoto extends Model
{
    protected $fillable = ['asset_repair_id', 'path', 'original_name'];

    public function repair()
    {
        return $this->belongsTo(AssetRepair::class, 'asset_repair_id');
    }
}
