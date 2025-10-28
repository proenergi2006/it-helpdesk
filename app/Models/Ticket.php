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
        'cabang',
        'title',
        'category',
        'description',
        'status',
        'taken_by',
        'started_at',
        'finished_at',
    ];

    public function takenByUser()
    {
        return $this->belongsTo(User::class, 'taken_by');
    }

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
