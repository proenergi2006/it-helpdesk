<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Label QR Aset</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, sans-serif; margin: 0; padding: 16px; background: #f3f4f6; }
        .bar { margin-bottom: 16px; display: flex; gap: 12px; align-items: center; }
        .bar button { padding: 8px 16px; border: 0; border-radius: 6px; background: #1d4ed8; color: #fff; cursor: pointer; }
        .bar span { font-size: 13px; color: #6b7280; }
        .grid { display: grid; grid-template-columns: repeat(auto-fill, 6.5cm); gap: 8px; }
        .label { width: 6.5cm; height: 3.4cm; background: #fff; border: 1px solid #111; border-radius: 4px;
                 display: flex; align-items: center; gap: 8px; padding: 6px; break-inside: avoid; }
        .qr { width: 2.6cm; height: 2.6cm; flex-shrink: 0; }
        .qr img, .qr canvas { width: 2.6cm !important; height: 2.6cm !important; }
        .info { min-width: 0; line-height: 1.25; }
        .code { font-size: 14px; font-weight: bold; }
        .name { font-size: 10px; margin-top: 2px; word-break: break-word; }
        .meta { font-size: 9px; color: #444; margin-top: 2px; }
        .brand { font-size: 8px; color: #666; margin-top: 4px; }
        @media print {
            body { background: #fff; padding: 0; }
            .bar { display: none; }
            .grid { gap: 4px; }
        }
    </style>
</head>
<body>
    <div class="bar">
        <button onclick="window.print()">Cetak</button>
        <span>{{ $assets->count() }} label. Pakai kertas stiker atau cetak lalu gunting.</span>
    </div>

    <div class="grid">
        @foreach ($assets as $asset)
            <div class="label">
                <div class="qr" data-url="{{ route('assets.scan', $asset->code) }}"></div>
                <div class="info">
                    <div class="code">{{ $asset->code }}</div>
                    <div class="name">{{ $asset->name }}</div>
                    <div class="meta">{{ $asset->cabang }}@if ($asset->serial_number) · SN {{ $asset->serial_number }}@endif</div>
                    <div class="brand">Scan untuk lapor masalah · IT Helpdesk</div>
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.querySelectorAll('.qr').forEach(el => {
            new QRCode(el, { text: el.dataset.url, width: 200, height: 200, correctLevel: QRCode.CorrectLevel.M });
        });
    </script>
</body>
</html>
