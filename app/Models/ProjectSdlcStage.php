<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectSdlcStage extends Model
{
    // required = wajib PDF sebelum tahap bisa ditandai selesai, optional = boleh upload, none = hanya tandai
    private const DOCUMENT_RULES = [
        'Requirement'   => ['rule' => 'required', 'label' => 'Dokumen Requirement'],
        'Design'        => ['rule' => 'required', 'label' => 'Dokumen Design'],
        'Development'   => ['rule' => 'required', 'label' => 'Dokumen Development'],
        'Testing / UAT' => ['rule' => 'required', 'label' => 'Dokumen UAT'],
        'Deployment'    => ['rule' => 'required', 'label' => 'Release Note'],
        'Maintenance'   => ['rule' => 'optional', 'label' => 'Dokumen Maintenance'],
    ];

    protected $fillable = [
        'project_id', 'name', 'position', 'done_at', 'done_by',
        'document_path', 'document_name', 'document_uploaded_by', 'document_uploaded_at',
    ];

    protected $casts = [
        'done_at' => 'datetime',
        'document_uploaded_at' => 'datetime',
    ];

    public static function requiredNames(): array
    {
        return array_keys(array_filter(self::DOCUMENT_RULES, fn ($r) => $r['rule'] === 'required'));
    }

    public function documentRule(): string
    {
        return self::DOCUMENT_RULES[$this->name]['rule'] ?? 'none';
    }

    public function documentLabel(): ?string
    {
        return self::DOCUMENT_RULES[$this->name]['label'] ?? null;
    }

    public function requiresDocument(): bool
    {
        return $this->documentRule() === 'required';
    }

    public function acceptsDocument(): bool
    {
        return $this->documentRule() !== 'none';
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function doneBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'done_by');
    }

    public function documentUploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'document_uploaded_by');
    }
}
