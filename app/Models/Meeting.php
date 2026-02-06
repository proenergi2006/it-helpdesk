<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Meeting extends Model
{
    protected $fillable = [
        'title','project_name','meeting_at','location','notes','attachment',
        'created_by','updated_by'
    ];

    protected $casts = [
        'meeting_at' => 'datetime',
    ];

    public function attendees()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function actionItems()
    {
        return $this->hasMany(MeetingActionItem::class)->latest();
    }

    public function creator(){ return $this->belongsTo(User::class, 'created_by'); }
    public function updater(){ return $this->belongsTo(User::class, 'updated_by'); }
}
