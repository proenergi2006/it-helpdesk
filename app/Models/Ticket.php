<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'email',
        'cc_emails',
        'cabang',
        'title',
        'category',
        'description',
        'status',
        'taken_by',
        'started_at',
        'finished_at',
        'priority',
        'klasifikasi',
        'user_id',
        'rating',
        'feedback_comment',
        'feedback_at',
        'is_confirmed_resolved',
        'approved_by',
        'approved_at',
        'approval_note',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_approval' => 'Menunggu Approval',
            'rejected' => 'Ditolak',
            default => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

    public function project()
    {
        return $this->hasOne(Project::class, 'ticket_id');
    }

    public function takenByUser()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'feedback_at' => 'datetime',
        'approved_at' => 'datetime',
        'taken_at' => 'datetime',
    'resolved_at' => 'datetime',
    ];

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_id');
    }
}
