<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>User Access Management Report</title>

    <style>
        @page {
            margin: 22px 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #1f2937;
            position: relative;
        }

        /* Watermark */
        .watermark {
            position: fixed;
            top: 42%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-32deg);
            font-size: 68px;
            font-weight: bold;
            color: rgba(220, 38, 38, 0.08);
            z-index: -1;
            white-space: nowrap;
        }

        /* Header halaman pertama */
        .header {
            border-bottom: 2px solid #1f2937;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .logo {
            width: 120px;
            height: auto;
        }

        .company-name {
            font-size: 14px;
            font-weight: bold;
            text-align: right;
            letter-spacing: 0.5px;
        }

        .report-title {
            font-size: 18px;
            font-weight: bold;
            margin-top: 10px;
            margin-bottom: 3px;
        }

        .subtitle {
            font-size: 11px;
            color: #6b7280;
        }

        .meta {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Satu user satu halaman */
        .page-user {
            page-break-inside: avoid;
            break-inside: avoid;
            margin-bottom: 0;
            padding-top: 2px;
        }

        .page-user:not(:last-child) {
            page-break-after: always;
            break-after: page;
        }

        .page-user:not(:first-child) {
            margin-top: 0;
        }

        /* Card user */
        .card {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 14px;
            margin-top: 0;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #111827;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
            text-align: left;
        }

        .info-table {
            margin-bottom: 12px;
        }

        .info-table td.label {
            width: 18%;
            background: #f9fafb;
            font-weight: bold;
        }

        .menu-table th,
        .menu-table td {
            text-align: center;
        }

        .menu-table th.menu-name,
        .menu-table td.menu-name,
        .menu-table th.module-name,
        .menu-table td.module-name {
            text-align: left;
        }

        .yes {
            color: #047857;
            font-weight: bold;
        }

        .no {
            color: #9ca3af;
        }

        .empty {
            color: #6b7280;
            font-style: italic;
            padding: 8px 0;
        }

        .footer-note {
            margin-top: 12px;
            font-size: 9px;
            color: #6b7280;
            text-align: right;
        }

        .footer-page {
            position: fixed;
            bottom: -6px;
            right: 0;
            font-size: 9px;
            color: #6b7280;
        }

        .footer-page:after {
            content: "Page " counter(page);
        }
    </style>
</head>
<body>
    <div class="watermark">CONFIDENTIAL</div>
    <div class="footer-page"></div>

    @forelse ($rows as $i => $row)
        <div class="page-user">

            @if ($loop->first)
                <div class="header">
                    <table class="header-table">
                        <tr>
                            <td style="width: 50%;">
                                <img src="{{ public_path('images/proenergi-logo.png') }}" class="logo" alt="Pro Energi Logo">
                            </td>
                            <td style="width: 50%;" class="company-name">
                                PT PRO ENERGI
                            </td>
                        </tr>
                    </table>

                    <div class="report-title">User Access Management Report</div>
                    <div class="subtitle">Daftar akses user internal beserta menu yang dapat diakses</div>
                    <div class="meta">
                        Dicetak pada: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>
            @endif

            <div class="card">
                <div class="section-title">
                    {{ $i + 1 }}. {{ strtoupper($row->nama_user) }}
                </div>

                <table class="info-table">
                    <tr>
                        <td class="label">Email</td>
                        <td>{{ $row->email ?? '-' }}</td>
                        <td class="label">Role</td>
                        <td>{{ $row->role ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Divisi</td>
                        <td>{{ $row->divisi ?? '-' }}</td>
                        <td class="label">Cabang</td>
                        <td>{{ $row->cabang ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Penanggung Jawab</td>
                        <td>{{ $row->penanggung_jawab ?? '-' }}</td>
                        <td class="label">Sistem</td>
                        <td>{{ $row->kategori_system ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td>{{ strtoupper($row->status ?? '-') }}</td>
                        <td class="label">Tanggal Resign</td>
                        <td>{{ optional($row->tgl_resign)->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Critical</td>
                        <td>{{ $row->is_critical ? 'YA' : 'TIDAK' }}</td>
                        <td class="label">Approval CEO</td>
                        <td>{{ $row->approval_ceo ? 'YA' : 'TIDAK' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Approval At</td>
                        <td>{{ optional($row->approval_at)->format('d/m/Y H:i') ?? '-' }}</td>
                        <td class="label">Workflow</td>
                        <td>{{ strtoupper(str_replace('_', ' ', $row->workflow_status ?? '-')) }}</td>
                    </tr>
                    <tr>
                        <td class="label">Disabled By</td>
                        <td>{{ $row->disabled_by ?? '-' }}</td>
                        <td class="label">Disabled At</td>
                        <td>{{ optional($row->disabled_at)->format('d/m/Y H:i') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Created By</td>
                        <td>{{ $row->created_by ?? '-' }}</td>
                        <td class="label">Updated By</td>
                        <td>{{ $row->updated_by ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Keterangan</td>
                        <td colspan="3">{{ $row->keterangan ?? '-' }}</td>
                    </tr>
                </table>

                <div class="section-title">Detail Menu Akses</div>

                @if ($row->menus && $row->menus->count())
                    <table class="menu-table">
                        <thead>
                            <tr>
                                <th style="width: 6%;">No</th>
                                <th class="menu-name" style="width: 24%;">Menu</th>
                                <th class="module-name" style="width: 24%;">Module</th>
                                <th style="width: 9%;">Create</th>
                                <th style="width: 9%;">View</th>
                                <th style="width: 9%;">Update</th>
                                <th style="width: 9%;">Delete</th>
                                <th style="width: 10%;">Approve</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($row->menus as $m)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="menu-name">{{ $m->menu_name }}</td>
                                    <td class="module-name">{{ $m->module ?? '-' }}</td>
                                    <td class="{{ $m->can_create ? 'yes' : 'no' }}">{{ $m->can_create ? 'YA' : '-' }}</td>
                                    <td class="{{ $m->can_view ? 'yes' : 'no' }}">{{ $m->can_view ? 'YA' : '-' }}</td>
                                    <td class="{{ $m->can_update ? 'yes' : 'no' }}">{{ $m->can_update ? 'YA' : '-' }}</td>
                                    <td class="{{ $m->can_delete ? 'yes' : 'no' }}">{{ $m->can_delete ? 'YA' : '-' }}</td>
                                    <td class="{{ $m->can_approve ? 'yes' : 'no' }}">{{ $m->can_approve ? 'YA' : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty">Tidak ada menu akses</div>
                @endif

                <div class="footer-note">
                    PT Pro Energi - User Access Management
                </div>
            </div>
        </div>
    @empty
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 50%;">
                        <img src="{{ public_path('images/proenergi-logo.png') }}" class="logo" alt="Pro Energi Logo">
                    </td>
                    <td style="width: 50%;" class="company-name">
                        PT PRO ENERGI
                    </td>
                </tr>
            </table>

            <div class="report-title">User Access Management Report</div>
            <div class="subtitle">Daftar akses user internal beserta menu yang dapat diakses</div>
            <div class="meta">
                Dicetak pada: {{ now()->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="empty">Tidak ada data</div>
    @endforelse
</body>
</html>