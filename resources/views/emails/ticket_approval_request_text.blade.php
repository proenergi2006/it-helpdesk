Ticket Menunggu Approval

ID Ticket  : #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
Nama User  : {{ $ticket->nama }}
Cabang     : {{ $ticket->cabang }}
Kategori   : {{ ucfirst($ticket->category) }}
Klasifikasi: {{ $ticket->klasifikasi }}
Judul      : {{ $ticket->title }}

Tinjau dan putuskan (setujui / tolak), link berlaku 3 hari dan khusus untuk Anda:
{{ $reviewUrl }}

Atau proses dari halaman Approval: {{ route('approvals.index') }}

Email ini dikirim otomatis oleh sistem Helpdesk IT Pro Energi.
