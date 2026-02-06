<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>MoM PDF</title>
    <style>
        @page { margin: 28px 28px 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color:#0f172a; }
        .muted { color:#64748b; }
        .small { font-size: 10px; }
        .h1 { font-size: 18px; font-weight: 700; margin: 0; }
        .h2 { font-size: 12px; font-weight: 700; margin: 0; color:#0f172a; }

        /* Header */
        .topbar {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 14px 16px;
            background: #f8fafc;
        }
        .row { width:100%; }
        .col { vertical-align: top; }
        .logo { height: 34px; }
        .doc-badge {
            display:inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            background: #0f172a;
            color: #fff;
            font-size: 10px;
            letter-spacing: .4px;
        }

        /* Cards */
        .card {
            margin-top: 14px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 14px;
            background: #ffffff;
        }
        .card-title {
            font-weight: 700;
            margin-bottom: 8px;
            color:#0f172a;
        }

        /* Meta grid */
        .meta {
            width:100%;
            border-collapse: collapse;
        }
        .meta td {
            padding: 6px 8px;
            border-bottom: 1px dashed #e2e8f0;
        }
        .meta td:first-child { width: 26%; color:#334155; font-weight: 700; }
        .meta td:last-child { color:#0f172a; }

        /* Chips */
        .chip {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #f1f5f9;
            color: #0f172a;
            font-size: 10px;
            margin: 0 6px 6px 0;
            border: 1px solid #e2e8f0;
        }

        /* Notes */
        .notes {
            line-height: 1.5;
        }
        .notes img { max-width: 100%; height: auto; border-radius: 10px; border:1px solid #e2e8f0; }
        .notes a { color:#2563eb; text-decoration: none; }
        .notes ul { margin: 6px 0 6px 18px; }
        .notes ol { margin: 6px 0 6px 18px; }

        /* Action items table */
        table.ai {
            width:100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        table.ai th, table.ai td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            vertical-align: top;
        }
        table.ai th {
            background: #0f172a;
            color: #fff;
            font-size: 11px;
            text-align: left;
        }
        .status {
            display:inline-block;
            padding: 3px 8px;
            border-radius: 999px;
            font-size: 10px;
            border: 1px solid #e2e8f0;
        }
        .st-todo { background:#fff7ed; color:#9a3412; border-color:#fed7aa; }
        .st-inp  { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
        .st-done { background:#ecfdf5; color:#047857; border-color:#a7f3d0; }

        /* Footer */
        .footer {
            margin-top: 14px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            color: #64748b;
            font-size: 10px;
        }
        .right { text-align:right; }

        /* Utilities for dompdf */
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
@php
    $logoPath = public_path('images/proenergi-logo.png'); // ganti kalau nama logo beda

    $statusLabel = [
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'done' => 'Done',
    ];

    $statusClass = function($st){
        return match($st){
            'todo' => 'st-todo',
            'in_progress' => 'st-inp',
            'done' => 'st-done',
            default => 'st-todo',
        };
    };
@endphp

<!-- HEADER -->
<div class="topbar">
    <table class="row">
        <tr>
            <td class="col" style="width:60%;">
                @if(file_exists($logoPath))
                    <img class="logo" src="{{ $logoPath }}" alt="Logo">
                @endif
                <div style="margin-top:6px;">
                    <div class="doc-badge">MEETING OF MEMORIES</div>
                </div>
            </td>
            <td class="col right" style="width:40%;">
                <div class="h2">IT Documentation</div>
                <div class="muted small">Generated: {{ now()->format('d M Y H:i') }}</div>
                <div class="muted small">MoM ID: MOM-{{ str_pad($meeting->id, 5, '0', STR_PAD_LEFT) }}</div>
            </td>
        </tr>
    </table>

    <div style="margin-top:10px;">
        <div class="h1">{{ $meeting->title }}</div>
        <div class="muted" style="margin-top:4px;">
            {{ $meeting->meeting_at->format('d M Y H:i') }}
            @if($meeting->location) · {{ $meeting->location }} @endif
            @if($meeting->project_name) · Project: <strong>{{ $meeting->project_name }}</strong> @endif
        </div>
    </div>
</div>

<!-- META -->
<div class="card">
    <div class="card-title">Meeting Details</div>
    <table class="meta">
        <tr>
            <td>When</td>
            <td class="nowrap">{{ $meeting->meeting_at->format('l, d M Y H:i') }}</td>
        </tr>
        <tr>
            <td>Location / Link</td>
            <td>{{ $meeting->location ?? '-' }}</td>
        </tr>
        <tr>
            <td>Project</td>
            <td>{{ $meeting->project_name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Created By</td>
            <td>{{ $meeting->creator?->name ?? '-' }}</td>
        </tr>
        <tr>
            <td>Last Update</td>
            <td>{{ $meeting->updated_at?->format('d M Y H:i') }}</td>
        </tr>
    </table>
</div>

<!-- ATTENDEES -->
<div class="card">
    <div class="card-title">Attendees</div>
    @if($meeting->attendees->count())
        @foreach($meeting->attendees as $a)
            <span class="chip">{{ $a->name }}</span>
        @endforeach
    @else
        <span class="muted">-</span>
    @endif
</div>

<!-- NOTES -->
<div class="card">
    <div class="card-title">Notes / Discussion</div>
    <div class="notes">
        {!! $meeting->notes ?: '<span class="muted">-</span>' !!}
    </div>
</div>

<!-- ACTION ITEMS -->
<div class="card">
    <div class="card-title">Action Items</div>

    <table class="ai">
        <thead>
            <tr>
                <th style="width:44%;">Task</th>
                <th style="width:20%;">Owner</th>
                <th style="width:16%;">Due</th>
                <th style="width:20%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($meeting->actionItems as $ai)
                <tr>
                    <td>{{ $ai->task }}</td>
                    <td>{{ $ai->owner?->name ?? '-' }}</td>
                    <td>{{ $ai->due_date?->format('d M Y') ?? '-' }}</td>
                    <td>
                        <span class="status {{ $statusClass($ai->status) }}">
                            {{ $statusLabel[$ai->status] ?? $ai->status }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="muted">Belum ada action item.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- FOOTER -->
<div class="footer">
    <table class="row">
        <tr>
            <td class="col">
                <span>Confidential · Internal Use Only</span>
            </td>
            <td class="col right">
                <span>IT Helpdesk System · MoM Export</span>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
