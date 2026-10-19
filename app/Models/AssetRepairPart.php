<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRepairPart extends Model
{
    protected $fillable = ['asset_repair_id', 'name', 'detail', 'qty', 'cost'];
}
