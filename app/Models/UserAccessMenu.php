<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAccessMenu extends Model
{
    use HasFactory;

    protected $table = 'user_access_menus';

    protected $fillable = [
        'user_access_id',
        'menu_name',
        'module',
        'can_create',
        'can_view',
        'can_update',
        'can_delete',
        'can_approve',
    ];

    protected $casts = [
        'can_create' => 'boolean',
        'can_view' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_approve' => 'boolean',
    ];

    public function userAccess()
    {
        return $this->belongsTo(UserAccessManagement::class, 'user_access_id');
    }
}