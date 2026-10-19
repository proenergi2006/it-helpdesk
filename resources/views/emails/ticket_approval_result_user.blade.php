<!DOCTYPE html>
<html lang="id">
<head><meta charset="UTF-8"><title>Hasil Approval Ticket</title></head>
<body style="font-family: Arial, sans-serif; background:#f5f7fb; padding:20px;">
    <div style="background:#fff; max-width:600px; margin:auto; border-radius:10px; padding:20px; border:1px solid #e5e7eb;">
        @if ($ticket->status === 'rejected')
            <h2 style="color:#dc2626; margin-top:0;">Ticket Ditolak</h2>
            <p>Ticket #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }} "{{ $ticket->title }}" ditolak.</p>
            @if ($ticket->approval_note)
                <p><strong>Alasan:</strong> {{ $ticket->approval_note }}</p>
            @endif
        @else
            <h2 style="color:#16a34a; margin-top:0;">Ticket Disetujui</h2>
            <p>Ticket #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }} "{{ $ticket->title }}" sudah disetujui dan akan segera diproses tim IT.</p>
        @endif
        <p style="margin-top:25px; font-size:12px; color:#6b7280;">Email ini dikirim otomatis oleh sistem Helpdesk IT Pro Energi.</p>
    </div>
</body>
</html>
