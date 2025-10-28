<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Report Tiket</title>
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background: #007bff;
            color: #fff;
        }
    </style>
</head>

<body>
    <h3 style="text-align:center;">Laporan Tiket Helpdesk IT</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Judul</th>
                <th>Cabang</th>
                <th>Kategori</th>
                <th>Status</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($tickets as $t)
                <tr>
                    <td>#{{ $t->id }}</td>
                    <td>{{ $t->nama }}</td>
                    <td>{{ $t->title }}</td>
                    <td>{{ $t->cabang }}</td>
                    <td>{{ ucfirst($t->category) }}</td>
                    <td>{{ ucfirst($t->status) }}</td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
