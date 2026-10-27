<?php

namespace App\Console\Commands;

use App\Models\Asset;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RenumberAssets extends Command
{
    protected $signature = 'assets:renumber
        {--apply : Jalankan perubahan (tanpa ini hanya menampilkan rencana)}';

    protected $description = 'Beri ulang nomor urut kode aset berdasarkan tanggal beli (terlama = 0001). Departemen dan cabang pada kode mengikuti data aset saat ini.';

    public function handle(): int
    {
        // pembelian awal lebih dulu; tanggal kosong di urutan terakhir, lalu berdasarkan ID
        $assets = Asset::query()
            ->orderByRaw('purchase_date is null')
            ->orderBy('purchase_date')
            ->orderBy('id')
            ->get();

        if ($assets->isEmpty()) {
            $this->info('Belum ada aset.');

            return self::SUCCESS;
        }

        $plan = $assets->values()->map(function (Asset $a, int $i) {
            $dept = $a->department ?: 'IT';
            $cabang = Asset::CABANG_CODES[$a->cabang] ?? 'NA';

            return ['asset' => $a, 'old' => $a->code, 'new' => sprintf('AST-%s-%s-%04d', $dept, $cabang, $i + 1)];
        });

        $changes = $plan->filter(fn ($p) => $p['old'] !== $p['new']);

        $this->table(['No', 'Tgl. beli', 'Kode lama', 'Kode baru', 'Nama'], $plan->map(fn ($p, $i) => [
            $i + 1, $p['asset']->purchase_date?->format('Y-m-d') ?? '(kosong)', $p['old'], $p['new'], $p['asset']->name,
        ])->all());

        $nodate = $assets->whereNull('purchase_date')->count();
        if ($nodate) {
            $this->warn("{$nodate} aset belum punya tanggal beli, diletakkan di urutan paling akhir.");
        }
        $this->line($changes->count().' dari '.$plan->count().' kode akan berubah.');

        if (!$this->option('apply')) {
            $this->warn('Dry-run: belum ada yang diubah. Backup database lebih dulu, lalu jalankan dengan --apply.');
            $this->warn('Label QR yang sudah tercetak dengan kode lama tidak berlaku lagi setelah ini, cetak ulang semuanya.');

            return self::SUCCESS;
        }

        // simpan pemetaan kode lama ke baru untuk arsip
        $csv = "kode_lama,kode_baru,nama\n".$plan->map(fn ($p) => implode(',', [$p['old'], $p['new'], '"'.str_replace('"', '""', $p['asset']->name).'"']))->implode("\n")."\n";
        $file = 'renumber-aset-'.now()->format('Ymd-His').'.csv';
        Storage::disk('local')->put($file, $csv);

        DB::transaction(function () use ($plan) {
            // dua tahap agar tidak bentrok dengan kode unik yang sedang dipakai aset lain
            foreach ($plan as $p) {
                DB::table('assets')->where('id', $p['asset']->id)->update(['code' => 'TMP-'.$p['asset']->id]);
            }
            foreach ($plan as $p) {
                DB::table('assets')->where('id', $p['asset']->id)->update(['code' => $p['new']]);
            }
        });

        $this->info($changes->count().' kode diubah. Pemetaan lama ke baru tersimpan di storage/app/private/'.$file);

        return self::SUCCESS;
    }
}
