<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Ticket Telah Selesai</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:20px;">

    <div
        style="background:#ffffff; max-width:600px; margin:auto; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">

        <h2 style="color:#16a34a; margin-top:0;">✔ Ticket Anda Telah Selesai</h2>

        <p>Halo {{ $ticket->nama }},</p>

        <p>Ticket Anda telah ditandai sebagai <strong>SELESAI</strong> oleh Tim IT.</p>

        <p>Berikut detail ticket Anda:</p>

        <table style="width:100%; border-collapse:collapse; margin-top:10px;">
            <tr>
                <td style="font-weight:bold; width:150px;">ID Ticket</td>
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
                <td style="font-weight:bold;">Deskripsi</td>
                <td>{{ $ticket->description }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Catatan Penyelesaian</td>
                <td>{{ $ticket->resolution_note ?? '-' }}</td>
            </tr>
        </table>

        <p style="margin-top:20px;">
            Terima kasih telah menggunakan layanan Helpdesk IT Pro Energi.
        </p>

        <p style="font-size:12px; color:#6b7280; margin-top:30px;">
            Email ini dikirim otomatis oleh sistem Helpdesk IT — PT Pro Energi.
        </p>

    </div>

</body>

</html>
