<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectActivity extends Model
{
    protected $fillable = ['project_id','user_id','event','title','meta'];
    protected $casts = ['meta' => 'array'];

    public function project(){ return $this->belongsTo(Project::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
