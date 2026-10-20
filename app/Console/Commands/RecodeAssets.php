<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecodeAssets extends Command
{
    protected $signature = 'assets:recode
        {--dept=IT : Departemen untuk aset lama yang belum punya departemen}
        {--cabang=HO : Singkatan cabang untuk kode jika aset lama belum punya cabang}
        {--apply : Jalankan perubahan (tanpa ini hanya menampilkan rencana)}';

    protected $description = 'Ubah kode aset lama (AST-0001) ke format AST-DEPT-CABANG-NOMOR. Kode lama disimpan sebagai alias agar QR lama tetap terbaca.';

    public function handle(): int
    {
        $dept = strtoupper($this->option('dept'));
        $defaultCabang = array_search(strtoupper($this->option('cabang')), Asset::CABANG_CODES, true);

        if (!array_key_exists($dept, Asset::DEPARTMENTS)) {
            $this->error("Departemen '{$dept}' tidak dikenal. Pilihan: ".implode(', ', array_keys(Asset::DEPARTMENTS)));

            return self::FAILURE;
        }
        if ($defaultCabang === false) {
            $this->error("Singkatan cabang '{$this->option('cabang')}' tidak dikenal. Pilihan: ".implode(', ', Asset::CABANG_CODES));

            return self::FAILURE;
        }

        $assets = Asset::where('code', 'regexp', '^AST-[0-9]+$')->orderBy('id')->get();

        if ($assets->isEmpty()) {
            $this->info('Tidak ada aset berkode lama.');

            return self::SUCCESS;
        }

        $plan = $assets->map(function (Asset $a) use ($dept, $defaultCabang) {
            $assetDept = $a->department ?: $dept;
            $cabangCode = Asset::CABANG_CODES[$a->cabang] ?? Asset::CABANG_CODES[$defaultCabang];

            return [
                'asset'   => $a,
                'old'     => $a->code,
                'new'     => sprintf('AST-%s-%s-%04d', $assetDept, $cabangCode, $a->id),
                'dept'    => $assetDept,
                'cabang'  => $a->cabang ?: "(kosong, kode memakai {$defaultCabang})",
            ];
        });

        $this->table(['ID', 'Kode lama', 'Kode baru', 'Nama', 'Cabang'], $plan->map(fn ($p) => [
            $p['asset']->id, $p['old'], $p['new'], $p['asset']->name, $p['cabang'],
        ])->all());

        if (!$this->option('apply')) {
            $this->warn('Dry-run: belum ada yang diubah. Tambahkan --apply setelah backup database.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($plan) {
            foreach ($plan as $p) {
                $p['asset']->forceFill([
                    'legacy_code' => $p['old'],
                    'code'        => $p['new'],
                    'department'  => $p['dept'],
                ])->save();
            }
        });

        $this->info($plan->count().' aset diubah. Kode lama tetap bisa dipindai (alias).');

        return self::SUCCESS;
    }
}
