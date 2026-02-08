<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    protected $fillable = [
        'name','category','description','status',
        'start_date','due_date','done_date',
        'assigned_to','created_by','updated_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date'   => 'date',
        'done_date'  => 'date',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function assignees(): BelongsToMany
{
    return $this->belongsToMany(User::class)->withTimestamps();
}

public function updates()
{
    return $this->hasMany(\App\Models\ProjectUpdate::class)->latest();
}

public function activities()
{
    return $this->hasMany(\App\Models\ProjectActivity::class)->latest();
}

public function logActivity(string $event, string $title, array $meta = []): void
{
    $this->activities()->create([
        'user_id' => auth()->id(),
        'event'   => $event,
        'title'   => $title,
        'meta'    => $meta ?: null,
    ]);
}

public function scopeOrdered($q)
{
    return $q->orderBy('position');
}

    
}
