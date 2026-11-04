<?php

namespace App\Support;

use App\Models\Asset;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

// Template import aset GA / Logistik (format Master Asset). Sheet pertama = data yang dibaca saat import.
class AssetGaTemplate
{
    private const ROWS = 3000;

    // kolom: header => [lebar, wajib, keterangan]
    private const COLUMNS = [
        'departemen'       => [12, true,  'GA atau LOG. Bila Anda login sebagai GA / Logistik, otomatis mengikuti departemen Anda.'],
        'kategori'         => [26, true,  'Pilih dari daftar (lihat sheet Daftar).'],
        'subkategori'      => [34, false, 'Pilih dari daftar subkategori sesuai kategori.'],
        'nama'             => [34, true,  'Nama aset, contoh: AC Daikin 1 PK.'],
        'merk'             => [16, false, ''],
        'model'            => [18, false, 'Merk / tipe.'],
        'serial_number'    => [20, false, 'Tidak boleh sama dengan aset lain.'],
        'no_aset_accurate' => [20, false, 'Nomor aset di Accurate. Bila sudah ada di sistem, data aset itu diperbarui (tidak dobel).'],
        'no_aset_lama'     => [26, false, 'Nomor di Master Asset lama, contoh: 20/03/02/Smd/04/23/004A.'],
        'cabang'           => [16, true,  'Pilih dari daftar. Dipakai membentuk kode aset.'],
        'lokasi'           => [28, false, 'Ruangan / sub lokasi, contoh: Office Pontianak.'],
        'pengguna'         => [20, false, 'PIC / pemegang. Nama atau email akun; bila tidak ada akunnya disimpan sebagai teks.'],
        'kelas'            => [12, false, 'asset atau inventory.'],
        'jumlah'           => [10, false, 'Angka, minimal 1.'],
        'harga_satuan'     => [16, false, 'Harga perolehan per unit, angka saja, contoh: 4700000.'],
        'tanggal_beli'     => [14, false, 'Format 2025-07-20 (tahun-bulan-tanggal) atau tanggal biasa Excel.'],
        'status'           => [12, false, 'aktif, dipinjam, rusak, diperbaiki, atau disposal. Kosong = aktif.'],
        'kondisi'          => [32, false, 'Keterangan kondisi, contoh: Baik.'],
        'status_ppn'       => [12, false, ''],
        'tanggal_disposal' => [16, false, 'Isi hanya bila aset sudah dilepas.'],
        'alasan_disposal'  => [24, false, ''],
        'no_ba_disposal'   => [18, false, 'Nomor Berita Acara disposal.'],
        'nilai_disposal'   => [16, false, 'Angka saja.'],
        'catatan'          => [30, false, ''],
    ];

    public static function build(): Spreadsheet
    {
        $book = new Spreadsheet();

        $data = $book->getActiveSheet();
        $data->setTitle('Data Aset');
        $guide = $book->createSheet();
        $guide->setTitle('Panduan');
        $lists = $book->createSheet();
        $lists->setTitle('Daftar');

        $listRanges = self::fillLists($lists);
        self::fillData($data, $listRanges);
        self::fillGuide($guide);

        $book->setActiveSheetIndex(0);

        return $book;
    }

    // Tulis daftar pilihan; kembalikan rentang sel untuk dropdown
    private static function fillLists(Worksheet $ws): array
    {
        $sets = [
            'kategori'    => Asset::gaTypes(),
            'subkategori' => array_merge(...array_values(Asset::GA_CATEGORIES)),
            'cabang'      => Asset::CABANG,
            'departemen'  => ['GA', 'LOG'],
            'kelas'       => ['asset', 'inventory'],
            'status'      => array_keys(Asset::STATUSES),
        ];

        $ranges = [];
        $col = 1;
        foreach ($sets as $name => $items) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $ws->setCellValue("{$letter}1", $name);
            $ws->getStyle("{$letter}1")->getFont()->setBold(true);
            foreach (array_values($items) as $i => $item) {
                $ws->setCellValue($letter.($i + 2), $item);
            }
            $ws->getColumnDimension($letter)->setWidth(36);
            $ranges[$name] = "=Daftar!\${$letter}\$2:\${$letter}\$".(count($items) + 1);
            $col++;
        }

        // kategori dan subkategori berdampingan agar mudah dicocokkan
        $row = 1;
        $ws->setCellValue('H1', 'kategori');
        $ws->setCellValue('I1', 'subkategori yang tersedia');
        $ws->getStyle('H1:I1')->getFont()->setBold(true);
        foreach (Asset::GA_CATEGORIES as $cat => $subs) {
            $ws->setCellValue('H'.++$row, $cat);
            $ws->setCellValue('I'.$row, implode('; ', $subs));
        }
        $ws->getColumnDimension('H')->setWidth(28);
        $ws->getColumnDimension('I')->setWidth(110);

        return $ranges;
    }

    private static function fillData(Worksheet $ws, array $ranges): void
    {
        $letters = [];
        $i = 1;
        foreach (self::COLUMNS as $header => [$width, $required]) {
            $letter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i++);
            $letters[$header] = $letter;
            $ws->setCellValue("{$letter}1", $header);
            $ws->getColumnDimension($letter)->setWidth($width);
            $ws->getStyle("{$letter}1")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => $required ? 'FFFFFF' : '1F2937']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $required ? 'C2410C' : 'FED7AA']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
        $ws->freezePane('A2');
        $last = end($letters);
        $ws->setAutoFilter("A1:{$last}1");

        // dropdown
        foreach (['departemen', 'kategori', 'subkategori', 'cabang', 'kelas', 'status'] as $header) {
            $dv = $ws->getDataValidation("{$letters[$header]}2:{$letters[$header]}".self::ROWS);
            $dv->setType(DataValidation::TYPE_LIST)
                ->setErrorStyle(DataValidation::STYLE_STOP)
                ->setAllowBlank(true)
                ->setShowDropDown(true)
                ->setShowErrorMessage(true)
                ->setErrorTitle('Nilai tidak ada di daftar')
                ->setError('Pilih salah satu nilai dari daftar (lihat sheet Daftar).')
                ->setFormula1($ranges[$header]);
        }

        // angka dan tanggal
        $ws->getStyle("{$letters['jumlah']}2:{$letters['jumlah']}".self::ROWS)->getNumberFormat()->setFormatCode('0');
        foreach (['harga_satuan', 'nilai_disposal'] as $h) {
            $ws->getStyle("{$letters[$h]}2:{$letters[$h]}".self::ROWS)->getNumberFormat()->setFormatCode('#,##0');
        }
        foreach (['tanggal_beli', 'tanggal_disposal'] as $h) {
            $ws->getStyle("{$letters[$h]}2:{$letters[$h]}".self::ROWS)->getNumberFormat()->setFormatCode('yyyy-mm-dd');
        }
    }

    private static function fillGuide(Worksheet $ws): void
    {
        $ws->setCellValue('A1', 'Panduan Template Import Aset GA / Logistik');
        $ws->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $steps = [
            '1. Isi sheet "Data Aset", satu aset (satu baris) per baris. Jangan ubah nama kolom di baris pertama dan jangan menambah sheet di depannya.',
            '2. Kolom berwarna oranye tua wajib diisi; oranye muda opsional.',
            '3. Kolom departemen, kategori, subkategori, cabang, kelas, dan status memakai dropdown. Pilihannya ada di sheet "Daftar".',
            '4. Barang yang sama banyak (misal 10 kursi) cukup satu baris dengan jumlah 10. Barang yang perlu dilacak satu per satu (laptop, kendaraan, mesin) satu baris per unit.',
            '5. Kode aset (contoh AST-GA-PTK-0001) dibuat otomatis oleh sistem, jangan diisi. Nomor lama dari Master Asset masukkan ke kolom no_aset_lama.',
            '6. Simpan sebagai .xlsx, lalu unggah lewat menu Aset > Import. Baris yang bermasalah dilewati dan dilaporkan; baris lain tetap masuk.',
            '7. Foto aset tidak ikut diimpor. Unggah satu per satu di halaman aset setelah data masuk.',
            '8. Maksimal ukuran file 5 MB.',
        ];
        foreach ($steps as $i => $text) {
            $ws->setCellValue('A'.($i + 3), $text);
        }

        $row = count($steps) + 5;
        $ws->setCellValue("A{$row}", 'Penjelasan kolom');
        $ws->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;
        foreach (['Kolom', 'Wajib', 'Keterangan'] as $c => $label) {
            $ws->setCellValueByColumnAndRow($c + 1, $row, $label);
        }
        $ws->getStyle("A{$row}:C{$row}")->getFont()->setBold(true);
        foreach (self::COLUMNS as $header => [, $required, $note]) {
            $row++;
            $ws->setCellValue("A{$row}", $header);
            $ws->setCellValue("B{$row}", $required ? 'Ya' : '');
            $ws->setCellValue("C{$row}", $note);
        }

        $row += 2;
        $ws->setCellValue("A{$row}", 'Contoh baris');
        $ws->getStyle("A{$row}")->getFont()->setBold(true);
        $example = [
            'departemen' => 'GA', 'kategori' => 'Peralatan Kantor', 'subkategori' => 'AC split kantor', 'nama' => 'AC Daikin 1 PK',
            'merk' => 'Daikin', 'model' => 'FTP25AV', 'cabang' => 'Pontianak', 'lokasi' => 'Office Pontianak', 'pengguna' => 'GA',
            'kelas' => 'inventory', 'jumlah' => 2, 'harga_satuan' => 4700000, 'tanggal_beli' => '2025-07-20', 'status' => 'aktif', 'kondisi' => 'Baik',
        ];
        $row++;
        $c = 1;
        foreach (array_keys(self::COLUMNS) as $header) {
            $ws->setCellValueByColumnAndRow($c, $row, $header);
            $ws->setCellValueByColumnAndRow($c, $row + 1, $example[$header] ?? '');
            $c++;
        }
        $ws->getStyle("A{$row}:".\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c - 1)."{$row}")->getFont()->setBold(true);

        $ws->getColumnDimension('A')->setWidth(22);
        $ws->getColumnDimension('B')->setWidth(8);
        $ws->getColumnDimension('C')->setWidth(90);
    }
}
