<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAudit extends Model
{
    protected $fillable = ['name', 'cabang', 'closed_at', 'created_by'];

    protected $casts = ['closed_at' => 'datetime'];

    public function items()
    {
        return $this->hasMany(AssetAuditItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return $this->closed_at === null;
    }

    // Aset yang wajib dicek: belum disposal, dan sesuai cabang bila opname dibatasi cabang
    public function assetsInScope()
    {
        return Asset::query()
            ->where('status', '!=', 'disposal')
            ->when($this->cabang, fn ($q) => $q->where('cabang', $this->cabang));
    }

    public static function current(): ?self
    {
        return static::whereNull('closed_at')->latest('id')->first();
    }
}
