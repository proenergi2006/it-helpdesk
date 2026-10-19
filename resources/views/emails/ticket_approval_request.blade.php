<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Approval Ticket</title></head>
<body style="font-family: Arial, sans-serif; background:#f5f7fb; padding:20px;">
    <div style="background:#fff; max-width:600px; margin:auto; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">
        <h2 style="color:#1d4ed8; margin-top:0;">Ticket Menunggu Approval</h2>
        <p>Ticket baru berikut perlu disetujui sebelum diproses tim IT:</p>
        <table style="width:100%; border-collapse:collapse;">
            <tr><td style="font-weight:bold; width:150px;">ID Ticket</td><td>#{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</td></tr>
            <tr><td style="font-weight:bold;">Nama User</td><td>{{ $ticket->nama }}</td></tr>
            <tr><td style="font-weight:bold;">Cabang</td><td>{{ $ticket->cabang }}</td></tr>
            <tr><td style="font-weight:bold;">Kategori</td><td>{{ ucfirst($ticket->category) }}</td></tr>
            <tr><td style="font-weight:bold;">Klasifikasi</td><td>{{ $ticket->klasifikasi }}</td></tr>
            <tr><td style="font-weight:bold;">Judul</td><td>{{ $ticket->title }}</td></tr>
            <tr><td style="font-weight:bold;">Deskripsi</td><td>{{ $ticket->description }}</td></tr>
        </table>
        <a href="{{ $reviewUrl }}"
            style="display:inline-block; margin-top:20px; padding:12px 20px; background:#16a34a; color:#fff; text-decoration:none; border-radius:6px; font-weight:bold;">
            Tinjau &amp; Putuskan (Setujui / Tolak)
        </a>
        <p style="margin-top:12px; font-size:13px; color:#6b7280;">
            Link ini berlaku 3 hari dan khusus untuk Anda. Anda juga bisa memproses ticket dari
            <a href="{{ route('approvals.index') }}" style="color:#1d4ed8;">halaman Approval</a>.
        </p>
        <p style="margin-top:25px; font-size:12px; color:#6b7280;">Email ini dikirim otomatis oleh sistem Helpdesk IT Pro Energi.</p>
    </div>
</body>
</html>
