<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserAccessManagement extends Model
{
    use HasFactory;

    protected $table = 'user_access_management';

    protected $fillable = [
        'nama_user',
        'email',
        'role',
        'divisi',
        'cabang',
        'penanggung_jawab',
        'status',
        'tgl_resign',
        'is_critical',
        'kategori_system',
        'menu_akses',
        'can_create',
        'can_view',
        'can_update',
        'can_delete',
        'can_approve',
        'approval_ceo',
        'approval_at',
        'workflow_status',
        'disabled_by',
        'disabled_at',
        'created_by',
        'updated_by',
        'keterangan',
        'lampiran',
    ];

    protected $casts = [
        'tgl_resign' => 'date',
        'is_critical' => 'boolean',
        'can_create' => 'boolean',
        'can_view' => 'boolean',
        'can_update' => 'boolean',
        'can_delete' => 'boolean',
        'can_approve' => 'boolean',
        'approval_ceo' => 'boolean',
        'approval_at' => 'datetime',
        'disabled_at' => 'datetime',
    ];

    public function menus()
    {
        return $this->hasMany(UserAccessMenu::class, 'user_access_id');
    }
}