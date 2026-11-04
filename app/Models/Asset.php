<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Asset extends Model
{
    public const TYPES = [
        'Laptop', 'PC / Desktop', 'Monitor', 'Printer', 'Scanner', 'Server',
        'Switch', 'Router', 'Access Point', 'CCTV', 'UPS', 'Lainnya',
    ];

    // Kategori aset tetap GA / Logistik => subkategori / contoh. Kolom type menyimpan kategori.
    public const GA_CATEGORIES = [
        'Tanah' => ['Tanah untuk bangunan kantor', 'Tanah untuk pool', 'Tanah parkir'],
        'Bangunan & Renovasi' => ['Bangunan kantor', 'Bangunan stall', 'Renovasi/perbaikan bangunan', 'Sistem AC sentral', 'Instalasi listrik & plumbing permanen'],
        'Peralatan & Mesin' => ['Mesin produksi', 'Genset (generator set)', 'AC (non permanen)', 'Kompresor', 'Alat las', 'Pompa air', 'Mesin pendingin industri', 'Mesin fotokopi besar'],
        'Peralatan Kantor' => ['Komputer & laptop', 'Printer & scanner', 'AC split kantor', 'Telepon & perangkat komunikasi', 'Mesin absensi', 'Mesin penghitung uang'],
        'Perabot & Fasilitas' => ['Meja & kursi', 'Lemari arsip', 'Partisi kantor', 'Sofa & perabot ruang tamu', 'Lampu hias permanen'],
        'Kendaraan' => ['Mobil operasional', 'Truk pengiriman', 'Motor dinas', 'Forklift', 'Alat berat'],
        'Peralatan IT & Elektronik' => ['Server', 'Router, switch, UPS', 'Monitor & proyektor', 'CCTV'],
        'Aset dalam Pengerjaan' => ['Pembangunan gedung belum selesai', 'Proyek instalasi mesin baru', 'Pemasangan sistem AC sentral (belum selesai)'],
        'Aset Tak Berwujud' => ['Hak paten', 'Lisensi software', 'Merek dagang', 'Goodwill'],
    ];

    // Penulisan lain di Master Asset lama => kategori baku (dipakai saat import)
    public const GA_CATEGORY_ALIASES = [
        'office equipment' => 'Peralatan Kantor', 'peralatan kantor' => 'Peralatan Kantor',
        'machinery & equipment' => 'Peralatan & Mesin', 'machinery and equipment' => 'Peralatan & Mesin', 'peralatan & mesin' => 'Peralatan & Mesin',
        'tanki / storage' => 'Peralatan & Mesin',
        'furniture & fixtures' => 'Perabot & Fasilitas', 'furniture and fixtures' => 'Perabot & Fasilitas', 'perabot & fasilitas' => 'Perabot & Fasilitas',
        'vehicles' => 'Kendaraan', 'vehicle' => 'Kendaraan', 'kendaraan' => 'Kendaraan',
        'interior' => 'Bangunan & Renovasi', 'building' => 'Bangunan & Renovasi',
        'it equipment' => 'Peralatan IT & Elektronik', 'peralatan it & elektronik' => 'Peralatan IT & Elektronik',
        'land' => 'Tanah', 'intangible' => 'Aset Tak Berwujud',
    ];

    public static function gaTypes(): array
    {
        return array_keys(self::GA_CATEGORIES);
    }

    // Role yang hanya boleh mengelola aset departemennya sendiri. IT melihat semua departemen.
    public const ROLE_DEPARTMENTS = ['ga' => 'GA', 'logistik' => 'LOG'];

    public const ASSET_CLASSES = ['asset' => 'Asset', 'inventory' => 'Inventory'];

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
        'LOG' => 'Logistik', 'OPS' => 'Operasional', 'MKT' => 'Marketing',
    ];

    protected $fillable = [
        'code', 'legacy_code', 'department', 'subcategory', 'spec_cpu', 'spec_ram', 'spec_storage', 'spec_os', 'spec_other', 'user_department', 'accurate_no', 'name', 'type', 'brand', 'model', 'serial_number', 'cabang', 'location',
        'asset_class', 'old_asset_no', 'qty', 'acquisition_amount', 'condition_note', 'vat_status',
        'disposed_at', 'disposal_reason', 'disposal_ba_no', 'disposal_amount',
        'user_id', 'user_name', 'photo_path', 'status', 'purchase_date', 'warranty_until', 'notes', 'created_by',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'warranty_until' => 'date',
        'disposed_at'    => 'date',
    ];

    // Pilihan jenis/kategori untuk dropdown: [grup => daftar]. GA / Logistik hanya melihat kategori GA.
    public static function typeOptions(): array
    {
        return self::restrictedDepartment()
            ? ['Kategori' => self::gaTypes()]
            : ['IT' => self::TYPES, 'GA / Logistik' => self::gaTypes()];
    }

    // Departemen yang dibatasi untuk user yang sedang login (null = tidak dibatasi)
    public static function restrictedDepartment(): ?string
    {
        return Auth::check() ? (self::ROLE_DEPARTMENTS[Auth::user()->role] ?? null) : null;
    }

    // Role yang boleh membuka modul aset: IT (semua) serta GA dan Logistik (departemen sendiri)
    public static function canAccessModule($user = null): bool
    {
        $user ??= Auth::user();

        return $user && ($user->role === 'it' || isset(self::ROLE_DEPARTMENTS[$user->role]));
    }

    // Boleh mengelola aset ini? (dipakai di luar global scope, mis. saat scan QR)
    public function isManageableBy($user): bool
    {
        if (!$user) {
            return false;
        }
        if ($user->role === 'it') {
            return true;
        }

        return (self::ROLE_DEPARTMENTS[$user->role] ?? null) === $this->department;
    }

    protected static function booted(): void
    {
        // GA dan Logistik hanya melihat aset departemennya sendiri, di semua halaman dan relasi
        static::addGlobalScope('department_access', function (Builder $query) {
            if ($dept = self::restrictedDepartment()) {
                $query->where($query->getModel()->getTable().'.department', $dept);
            }
        });

        // kode aset otomatis: AST-{DEPT}-{CABANG}-{NOMOR}, contoh AST-IT-JKT-0012.
        // Dikunci saat aset dibuat dan tidak berubah walau aset dimutasi.
        // Nomor urut dihitung per departemen (IT, GA, LOG masing-masing mulai dari 0001).
        static::created(function (Asset $asset) {
            if ($asset->code) {
                return;
            }

            // bila dua aset disimpan bersamaan dan mendapat nomor sama, indeks unik menolak yang kedua; ulangi
            for ($try = 0; ; $try++) {
                try {
                    $asset->forceFill(['code' => self::makeCode($asset)])->saveQuietly();

                    return;
                } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
                    if ($try >= 5) {
                        throw $e;
                    }
                }
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

        return sprintf('AST-%s-%s-%04d', $dept, $cabang, self::nextNumber($dept));
    }

    // Nomor urut berikutnya dalam satu departemen (tertinggi yang ada + 1, lintas cabang)
    public static function nextNumber(string $dept): int
    {
        $max = static::withoutGlobalScopes()
            ->where('code', 'like', "AST-{$dept}-%")
            ->pluck('code')
            ->map(fn ($c) => (int) substr(strrchr($c, '-'), 1))
            ->max();

        return ($max ?? 0) + 1;
    }

    public function getDepartmentNameAttribute(): ?string
    {
        return $this->department ? (self::DEPARTMENTS[$this->department] ?? $this->department) : null;
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path ? asset('storage/'.$this->photo_path) : null;
    }

    // Dokumen wajib: foto aset (kolom photo_path) + tiga dokumen di tabel asset_documents
    public const DOCUMENT_TYPES = [
        'box'     => 'Foto kode pada dus (serial number, MO, type)',
        'serial'  => 'Foto serial number pada unit',
        'receipt' => 'Tanda terima user (PDF)',
    ];

    public function checks()
    {
        return $this->hasMany(AssetCheck::class)->latest('id');
    }

    public function documents()
    {
        return $this->hasMany(AssetDocument::class);
    }

    public function document(string $type): ?AssetDocument
    {
        return $this->documents->firstWhere('type', $type);
    }

    // Berapa dari 4 dokumen yang sudah ada (foto aset, dus, serial, tanda terima)
    public function getDocumentCountAttribute(): int
    {
        return ($this->photo_path ? 1 : 0) + $this->documents->whereIn('type', array_keys(self::DOCUMENT_TYPES))->count();
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
     * Pindahkan aset (cabang / lokasi / pengguna / departemen pengguna) dan catat riwayatnya.
     * $extra: from_cabang (cabang asal bila ingin dicatat lain dari posisi sekarang), photo_path, receipt_path.
     * Mengembalikan false jika tidak ada yang berubah.
     */
    public function mutateTo(array $to, ?string $reason = null, $date = null, ?int $by = null, array $extra = []): bool
    {
        $blank = fn ($v) => ($v === null || trim((string) $v) === '') ? null : trim((string) $v);

        $to['cabang'] = $blank($to['cabang'] ?? null);
        $to['location'] = $blank($to['location'] ?? null);
        $newUserId = $blank($to['user_id'] ?? null) !== null ? (int) $to['user_id'] : null;
        $newUserName = $newUserId ? null : $blank($to['user_name'] ?? null);
        $newUserLabel = $newUserId ? User::find($newUserId)?->name : $newUserName;
        $newUserDept = $blank($to['user_department'] ?? null);

        $same = ($to['cabang'] ?? null) === $this->cabang
            && ($to['location'] ?? null) === $this->location
            && $newUserId === $this->user_id
            && $newUserName === $this->user_name
            && $newUserDept === $this->user_department;

        if ($same) {
            return false;
        }

        $this->mutations()->create([
            'mutated_at'           => $date ?? now()->toDateString(),
            'from_cabang'          => $blank($extra['from_cabang'] ?? null) ?? $this->cabang,
            'from_location'        => $this->location,
            'from_user'            => $this->holder_name,
            'from_user_department' => $this->user_department,
            'to_cabang'            => $to['cabang'] ?? null,
            'to_location'          => $to['location'] ?? null,
            'to_user'              => $newUserLabel,
            'to_user_department'   => $newUserDept,
            'reason'               => $reason,
            'photo_path'           => $extra['photo_path'] ?? null,
            'receipt_path'         => $extra['receipt_path'] ?? null,
            'created_by'           => $by,
        ]);

        $this->update([
            'cabang'          => $to['cabang'] ?? null,
            'location'        => $to['location'] ?? null,
            'user_id'         => $newUserId,
            'user_name'       => $newUserName,
            'user_department' => $newUserDept,
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
