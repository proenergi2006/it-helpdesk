<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Ticket Baru Masuk</title>
</head>

<body style="font-family: Arial, sans-serif; background:#f5f7fb; padding:20px;">

    <div
        style="background:#ffffff; max-width:600px; margin:auto; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">

        <h2 style="color:#1d4ed8; margin-top:0;">🎟️ Ticket Baru Masuk</h2>

        <p>Halo Tim IT,</p>
        <p>Ada ticket baru yang masuk. Berikut detail lengkapnya:</p>

        <table style="width:100%; margin-top:15px; border-collapse:collapse;">
            <tr>
                <td style="font-weight:bold; width:150px;">ID Ticket</td>
                <td>#{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Nama User</td>
                <td>{{ $ticket->nama }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Email User</td>
                <td>{{ $ticket->email }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Cabang</td>
                <td>{{ $ticket->cabang }}</td>
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
                <td style="font-weight:bold;">Judul Ticket</td>
                <td>{{ $ticket->title }}</td>
            </tr>
            <tr>
                <td style="font-weight:bold;">Deskripsi</td>
                <td>{{ $ticket->description }}</td>
            </tr>
        </table>

        <p style="margin-top:20px;">
            Silakan login ke dashboard Helpdesk IT untuk memproses ticket ini.
        </p>

        <a href="https://helpdesk.proenergi.com/login"
            style="display:inline-block; margin-top:10px; padding:10px 15px; background:#1d4ed8; color:#fff; text-decoration:none; border-radius:6px;">
            Buka Dashboard Helpdesk
        </a>

        <p style="margin-top:25px; font-size:12px; color:#6b7280;">
            Email ini dikirim otomatis oleh sistem Helpdesk IT Pro Energi.
        </p>
    </div>

</body>

</html>
