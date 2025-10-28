<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <title>Laporan Tiket Helpdesk IT</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }

        h3 {
            text-align: center;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            font-size: 11px;
            color: #666;
            margin-top: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .text-center {
            text-align: center;
        }

        .small {
            font-size: 10px;
        }
    </style>
</head>

<body>
    <h3>Laporan Tiket Helpdesk IT</h3>
    <p>Periode: {{ $start ? \Carbon\Carbon::parse($start)->format('d M Y') : '-' }}
        s/d {{ $end ? \Carbon\Carbon::parse($end)->format('d M Y') : '-' }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Judul</th>
                <th>Cabang</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Dikerjakan Oleh</th>
                <th>Durasi</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $t)
                @php
                    $durasi = '-';
                    if ($t->started_at && $t->finished_at) {
                        $diff = $t->started_at->diff($t->finished_at);
                        $durasi =
                            ($diff->h ? $diff->h . ' jam ' : '') .
                            ($diff->i ? $diff->i . ' menit ' : '') .
                            ($diff->s ? $diff->s . ' detik' : '');
                    }
                @endphp
                <tr>
                    <td class="text-center">#{{ $t->id }}</td>
                    <td>{{ $t->nama }}</td>
                    <td>{{ $t->title ?: '-' }}</td>
                    <td>{{ $t->cabang }}</td>
                    <td>{{ ucfirst($t->category) }}</td>
                    <td>{{ ucfirst($t->status) }}</td>
                    <td>{{ $t->takenByUser?->name ?? '-' }}</td>
                    <td>{{ $durasi }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center small">Tidak ada data tiket untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p class="small text-center" style="margin-top: 15px;">
        Dicetak oleh sistem Helpdesk IT · PT Pro Energi — {{ now()->format('d/m/Y H:i') }}
    </p>
</body>

</html>
