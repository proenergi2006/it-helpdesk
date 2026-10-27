<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDocument extends Model
{
    protected $fillable = ['asset_id', 'type', 'path', 'original_name', 'mime', 'uploaded_by'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }
}
