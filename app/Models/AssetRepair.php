<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetRepair extends Model
{
    protected $fillable = ['asset_id', 'ticket_id', 'repaired_at', 'problem', 'action', 'vendor', 'created_by'];

    protected $casts = ['repaired_at' => 'date'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function photos()
    {
        return $this->hasMany(AssetRepairPhoto::class);
    }

    public function parts()
    {
        return $this->hasMany(AssetRepairPart::class);
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->parts->sum(fn ($p) => $p->qty * (float) $p->cost);
    }
}
