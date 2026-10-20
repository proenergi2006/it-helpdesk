<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    public const TYPES = [
        'Laptop', 'PC / Desktop', 'Monitor', 'Printer', 'Scanner', 'Server',
        'Switch', 'Router', 'Access Point', 'CCTV', 'UPS', 'Lainnya',
    ];

    public const STATUSES = [
        'aktif'     => 'Aktif',
        'dipinjam'  => 'Dipinjam',
        'rusak'     => 'Rusak',
        'diperbaiki' => 'Diperbaiki',
        'disposal'  => 'Disposal',
    ];

    public const CABANG = [
        'Head Office', 'Jakarta', 'Surabaya', 'Samarinda',
        'Palembang', 'Banjarmasin', 'Pontianak', 'Sulawesi',
    ];

    // Singkatan cabang untuk kode aset. Setelah dipakai di label, jangan diubah.
    public const CABANG_CODES = [
        'Head Office' => 'HO', 'Jakarta' => 'JKT', 'Surabaya' => 'SBY', 'Samarinda' => 'SMD',
        'Palembang' => 'PLG', 'Banjarmasin' => 'BJM', 'Pontianak' => 'PTK', 'Sulawesi' => 'SLW',
    ];

    // Departemen pemilik aset: singkatan => nama. Sesuaikan dengan daftar departemen perusahaan.
    public const DEPARTMENTS = [
        'IT' => 'IT', 'FIN' => 'Finance', 'HR' => 'HR', 'GA' => 'General Affair',
        'OPS' => 'Operasional', 'MKT' => 'Marketing',
    ];

    protected $fillable = [
        'code', 'legacy_code', 'department', 'accurate_no', 'name', 'type', 'brand', 'model', 'serial_number', 'cabang', 'location',
        'user_id', 'user_name', 'photo_path', 'status', 'purchase_date', 'warranty_until', 'notes', 'created_by',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'warranty_until' => 'date',
    ];

    protected static function booted(): void
    {
        // kode aset otomatis: AST-{DEPT}-{CABANG}-{NOMOR}, contoh AST-IT-JKT-0012.
        // Dikunci saat aset dibuat dan tidak berubah walau aset dimutasi.
        // Nomor memakai ID aset sehingga satu rangkaian untuk semua dan tidak bisa bentrok.
        static::created(function (Asset $asset) {
            if (!$asset->code) {
                $asset->forceFill(['code' => self::makeCode($asset)])->saveQuietly();
            }
        });
    }

    // Cari berdasarkan kode saat ini atau kode lama (QR lama tetap terbaca)
    public function scopeByCode($query, string $code)
    {
        $code = strtoupper(trim($code));

        return $query->where(fn ($q) => $q->where('code', $code)->orWhere('legacy_code', $code));
    }

    public static function makeCode(Asset $asset): string
    {
        $dept = $asset->department ?: 'IT';
        $cabang = self::CABANG_CODES[$asset->cabang] ?? 'NA';

        return sprintf('AST-%s-%s-%04d', $dept, $cabang, $asset->id);
    }

    public function getDepartmentNameAttribute(): ?string
    {
        return $this->department ? (self::DEPARTMENTS[$this->department] ?? $this->department) : null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Pengguna dari akun user; jika tidak ada akun, pakai teks bebas
    public function getHolderNameAttribute(): ?string
    {
        return $this->user?->name ?? $this->user_name;
    }

    public function repairs()
    {
        return $this->hasMany(AssetRepair::class)->latest('repaired_at')->latest('id');
    }

    public function mutations()
    {
        return $this->hasMany(AssetMutation::class)->latest('mutated_at')->latest('id');
    }

    /**
     * Pindahkan aset (cabang / lokasi / pengguna) dan catat riwayatnya.
     * Mengembalikan false jika tidak ada yang berubah.
     */
    public function mutateTo(array $to, ?string $reason = null, $date = null, ?int $by = null): bool
    {
        $blank = fn ($v) => ($v === null || trim((string) $v) === '') ? null : trim((string) $v);

        $to['cabang'] = $blank($to['cabang'] ?? null);
        $to['location'] = $blank($to['location'] ?? null);
        $newUserId = $blank($to['user_id'] ?? null) !== null ? (int) $to['user_id'] : null;
        $newUserName = $newUserId ? null : $blank($to['user_name'] ?? null);
        $newUserLabel = $newUserId ? User::find($newUserId)?->name : $newUserName;

        $same = ($to['cabang'] ?? null) === $this->cabang
            && ($to['location'] ?? null) === $this->location
            && $newUserId === $this->user_id
            && $newUserName === $this->user_name;

        if ($same) {
            return false;
        }

        $this->mutations()->create([
            'mutated_at'    => $date ?? now()->toDateString(),
            'from_cabang'   => $this->cabang,
            'from_location' => $this->location,
            'from_user'     => $this->holder_name,
            'to_cabang'     => $to['cabang'] ?? null,
            'to_location'   => $to['location'] ?? null,
            'to_user'       => $newUserLabel,
            'reason'        => $reason,
            'created_by'    => $by,
        ]);

        $this->update([
            'cabang'    => $to['cabang'] ?? null,
            'location'  => $to['location'] ?? null,
            'user_id'   => $newUserId,
            'user_name' => $newUserName,
        ]);

        return true;
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class)->latest();
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }

    public function getWarrantyStateAttribute(): ?string
    {
        if (!$this->warranty_until) {
            return null;
        }

        if ($this->warranty_until->isPast()) {
            return 'habis';
        }

        return $this->warranty_until->diffInDays(now()) <= 30 ? 'segera' : 'aktif';
    }
}
