<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ticket Anda Dibatalkan</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:20px;">

    <div
        style="
        background:#ffffff;
        max-width:600px;
        margin:auto;
        border-radius:10px;
        padding:20px;
        border:1px solid #e5e7eb;
    ">

        <!-- Header -->
        <h2 style="color:#dc2626; margin-top:0;">
            ❌ Ticket Dibatalkan
        </h2>

        <p>Halo <strong>{{ $ticket->nama }}</strong>,</p>
        <p>
            Ticket Anda telah dibatalkan oleh tim IT. Berikut informasi lengkapnya:
        </p>

        <!-- Detail Ticket -->
        <table style="width:100%; margin-top:15px; border-collapse:collapse;">
            <tr>
                <td style="font-weight:bold; width:140px;">ID Ticket</td>
                <td>#{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Judul</td>
                <td>{{ $ticket->title }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Kategori</td>
                <td>{{ ucfirst($ticket->category) }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Klasifikasi</td>
                <td>{{ $ticket->klasifikasi }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Cabang</td>
                <td>{{ $ticket->cabang }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Dibatalkan Pada</td>
                <td>{{ $ticket->finished_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <!-- Catatan Pembatalan -->
        <div
            style="
            margin-top:20px;
            background:#fef2f2;
            border-left:4px solid #dc2626;
            padding:12px 15px;
            border-radius:6px;
        ">
            <strong>Alasan Pembatalan:</strong>
            <p style="margin:6px 0 0; color:#7f1d1d;">
                {{ $ticket->cancel_note }}
            </p>
        </div>

        <!-- Penutup -->
        <p style="margin-top:20px;">
            Jika Anda masih membutuhkan bantuan, silakan membuat ticket baru melalui sistem Helpdesk IT.
        </p>

        <p style="margin-top:25px; font-size:12px; color:#6b7280;">
            Email ini dikirim otomatis oleh sistem Helpdesk IT Pro Energi.
        </p>

    </div>

</body>

</html>
