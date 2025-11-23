<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ticket Anda Berhasil Dibuat</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f8fafc; padding:20px;">

    <div
        style="background:#ffffff; max-width:600px; margin:auto; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">

        <h2 style="color:#16a34a; margin-top:0;">✔ Ticket Berhasil Dibuat</h2>

        <p>Halo {{ $ticket->nama }},</p>
        <p>Ticket Anda telah berhasil dikirim. Berikut detailnya:</p>

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
        </table>

        <p style="margin-top:20px;">
            Tim IT sedang memproses ticket Anda. Anda akan menerima update ketika status ticket berubah.
        </p>

        <p style="margin-top:25px; font-size:12px; color:#6b7280;">
            Email ini dikirim otomatis oleh sistem Helpdesk IT Pro Energi.
        </p>

    </div>

</body>

</html>
