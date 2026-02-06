<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MeetingActionItem extends Model
{
    protected $fillable = ['meeting_id','task','owner_id','due_date','status'];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function meeting(){ return $this->belongsTo(Meeting::class); }
    public function owner(){ return $this->belongsTo(User::class, 'owner_id'); }
}
