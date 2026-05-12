<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #1e293b; }

        .header-top { background: #1e3a5f; color: white; padding: 12px 16px; margin-bottom: 10px; }
        .header-top h1 { font-size: 14px; font-weight: bold; margin-bottom: 2px; }
        .header-top p { font-size: 9px; color: #93c5fd; }

        .info-grid { display: table; width: 100%; margin-bottom: 10px; border: 1px solid #e2e8f0; border-radius: 4px; }
        .info-grid td { padding: 4px 10px; font-size: 9px; }
        .info-label { color: #64748b; width: 110px; }
        .info-value { font-weight: bold; color: #1e293b; }

        .section-title { font-size: 10px; font-weight: bold; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; padding-bottom: 3px; margin: 10px 0 6px; }

        .kpi-grid { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .kpi-grid td { width: 20%; padding: 6px 8px; border: 1px solid #e2e8f0; vertical-align: top; }
        .kpi-label { font-size: 7px; color: #64748b; margin-bottom: 2px; }
        .kpi-value { font-size: 11px; font-weight: bold; }
        .kpi-sub { font-size: 7px; margin-top: 1px; }

        table.main { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        table.main th {
            background: #1e3a5f; color: white;
            padding: 5px 4px; text-align: center;
            font-size: 7.5px; font-weight: bold;
        }
        table.main td {
            padding: 4px 5px; border-bottom: 1px solid #e2e8f0;
            text-align: right; font-size: 8px;
        }
        table.main td:first-child,
        table.main td:nth-child(2) { text-align: left; }
        table.main tr:nth-child(even) td { background: #f8fafc; }

        .good  { color: #16a34a; font-weight: bold; }
        .bad   { color: #dc2626; font-weight: bold; }
        .warn  { color: #ca8a04; font-weight: bold; }

        .interpretasi { border: 1px solid #e2e8f0; border-radius: 4px; padding: 8px 10px; margin-bottom: 10px; }
        .zone-box { padding: 6px 10px; border-radius: 4px; }
        .zone-iv  { background: #fef2f2; border: 1px solid #fecaca; }
        .zone-iii { background: #fffbeb; border: 1px solid #fde68a; }
        .zone-ii  { background: #fffbeb; border: 1px solid #fde68a; }
        .zone-i   { background: #f0fdf4; border: 1px solid #bbf7d0; }
        .zone-title { font-size: 10px; font-weight: bold; margin-bottom: 3px; }
        .zone-detail { font-size: 8px; color: #475569; }

        .formula-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .formula-table th { background: #f1f5f9; padding: 4px 6px; font-size: 8px; text-align: left; border: 1px solid #e2e8f0; }
        .formula-table td { padding: 3px 6px; font-size: 8px; border: 1px solid #e2e8f0; }

        .footer { text-align: right; font-size: 8px; color: #94a3b8; margin-top: 12px; border-top: 1px solid #e2e8f0; padding-top: 6px; }

        .badge-over  { background: #fef2f2; color: #dc2626; padding: 1px 6px; border-radius: 3px; font-weight: bold; }
        .badge-on    { background: #fffbeb; color: #ca8a04; padding: 1px 6px; border-radius: 3px; font-weight: bold; }
        .badge-under { background: #f0fdf4; color: #16a34a; padding: 1px 6px; border-radius: 3px; font-weight: bold; }
    </style>
</head>
<body>

{{-- HEADER --}}
<div class="header-top">
    <h1>LAPORAN EARNED VALUE ANALYSIS (EVA)</h1>
    <p>{{ $project->name }} &mdash; {{ $project->project_code }}</p>
</div>

{{-- INFO PROYEK --}}
<div class="section-title">Informasi Proyek</div>
<table class="info-grid">
    <tr>
        <td class="info-label">Nama Pekerjaan</td>
        <td class="info-value">{{ $project->name }}</td>
        <td class="info-label">No. Kontrak</td>
        <td class="info-value">{{ $project->contract_number ?? '—' }}</td>
    </tr>
    <tr>
        <td class="info-label">Lokasi</td>
        <td class="info-value">{{ $project->location ?? '—' }}</td>
        <td class="info-label">Pemilik</td>
        <td class="info-value">{{ $project->owner ?? '—' }}</td>
    </tr>
    <tr>
        <td class="info-label">Periode Proyek</td>
        <td class="info-value">
            {{ $project->start_date->format('d/m/Y') }} —
            {{ $project->end_date->format('d/m/Y') }}
        </td>
        <td class="info-label">BAC</td>
        <td class="info-value">Rp {{ number_format($project->bac, 0, ',', '.') }}</td>
    </tr>
    @if($latest)
    <tr>
        <td class="info-label">Periode Terbaru</td>
        <td class="info-value">
            {{ $latest->period_label }}
            ({{ $latest->report_date->format('d/m/Y') }})
        </td>
        <td class="info-label">Tanggal Cetak</td>
        <td class="info-value">{{ now()->format('d/m/Y H:i') }}</td>
    </tr>
    @endif
</table>

@if($latest)
{{-- KPI PERIODE TERBARU --}}
<div class="section-title">Ringkasan Kinerja Periode Terbaru ({{ $latest->period_label }})</div>
<table class="kpi-grid">
    <tr>
        <td>
            <div class="kpi-label">Planned Value (PV)</div>
            <div class="kpi-value" style="color:#2563eb">
                Rp {{ number_format($latest->planned_value/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub" style="color:#64748b">Nilai rencana kumulatif</div>
        </td>
        <td>
            <div class="kpi-label">Earned Value (EV)</div>
            <div class="kpi-value" style="color:#16a34a">
                Rp {{ number_format($latest->earned_value/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub" style="color:#64748b">Nilai pekerjaan selesai</div>
        </td>
        <td>
            <div class="kpi-label">Actual Cost (AC)</div>
            <div class="kpi-value" style="color:#d97706">
                Rp {{ number_format($latest->actual_cost/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub" style="color:#64748b">Biaya aktual dikeluarkan</div>
        </td>
        <td>
            <div class="kpi-label">Cost Variance (CV)</div>
            <div class="kpi-value {{ $latest->cost_variance >= 0 ? 'good' : 'bad' }}">
                Rp {{ number_format($latest->cost_variance/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub {{ $latest->cost_variance >= 0 ? 'good' : 'bad' }}">
                {{ $latest->cost_variance >= 0 ? 'Under Budget' : 'Cost Overrun' }}
            </div>
        </td>
        <td>
            <div class="kpi-label">Schedule Variance (SV)</div>
            <div class="kpi-value {{ $latest->schedule_variance >= 0 ? 'good' : 'bad' }}">
                Rp {{ number_format($latest->schedule_variance/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub {{ $latest->schedule_variance >= 0 ? 'good' : 'bad' }}">
                {{ $latest->schedule_variance >= 0 ? 'On Schedule' : 'Behind Schedule' }}
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="kpi-label">CPI</div>
            <div class="kpi-value {{ $latest->cpi >= 1 ? 'good' : ($latest->cpi >= 0.95 ? 'warn' : 'bad') }}">
                {{ number_format($latest->cpi, 4) }}
            </div>
            <div class="kpi-sub {{ $latest->cpi >= 1 ? 'good' : ($latest->cpi >= 0.95 ? 'warn' : 'bad') }}">
                {{ $latest->cpi >= 1 ? 'Under Budget' : ($latest->cpi >= 0.95 ? 'At Risk' : 'Over Budget') }}
            </div>
        </td>
        <td>
            <div class="kpi-label">SPI</div>
            <div class="kpi-value {{ $latest->spi >= 1 ? 'good' : ($latest->spi >= 0.95 ? 'warn' : 'bad') }}">
                {{ number_format($latest->spi, 4) }}
            </div>
            <div class="kpi-sub {{ $latest->spi >= 1 ? 'good' : ($latest->spi >= 0.95 ? 'warn' : 'bad') }}">
                {{ $latest->spi >= 1 ? 'On Schedule' : ($latest->spi >= 0.95 ? 'At Risk' : 'Behind Schedule') }}
            </div>
        </td>
        <td>
            <div class="kpi-label">EAC</div>
            <div class="kpi-value" style="color:#475569">
                Rp {{ number_format($latest->eac/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub" style="color:#64748b">Estimate at Completion</div>
        </td>
        <td>
            <div class="kpi-label">ETC</div>
            <div class="kpi-value" style="color:#475569">
                Rp {{ number_format($latest->etc/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub" style="color:#64748b">Estimate to Complete</div>
        </td>
        <td>
            <div class="kpi-label">VAC</div>
            <div class="kpi-value {{ $latest->vac >= 0 ? 'good' : 'bad' }}">
                Rp {{ number_format($latest->vac/1000000, 2, ',', '.') }}Jt
            </div>
            <div class="kpi-sub {{ $latest->vac >= 0 ? 'good' : 'bad' }}">
                {{ $latest->vac >= 0 ? 'Surplus' : 'Defisit' }}
            </div>
        </td>
    </tr>
</table>
@endif

{{-- TABEL REKAP SEMUA PERIODE --}}
<div class="section-title">Rekap EVA Semua Periode</div>
<table class="main">
    <thead>
        <tr>
            <th>Periode</th>
            <th>Tanggal</th>
            <th>PV (Rp)</th>
            <th>EV (Rp)</th>
            <th>AC (Rp)</th>
            <th>SV (Rp)</th>
            <th>CV (Rp)</th>
            <th>SPI</th>
            <th>CPI</th>
            <th>EAC (Rp)</th>
            <th>ETC (Rp)</th>
            <th>VAC (Rp)</th>
            <th>TCPI</th>
            <th>Progress</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($evaRecords as $rec)
        <tr>
            <td style="text-align:left; font-weight:bold;">{{ $rec->period_label }}</td>
            <td style="text-align:left;">{{ $rec->report_date->format('d/m/Y') }}</td>
            <td>{{ number_format($rec->planned_value, 0, ',', '.') }}</td>
            <td>{{ number_format($rec->earned_value, 0, ',', '.') }}</td>
            <td>{{ number_format($rec->actual_cost, 0, ',', '.') }}</td>
            <td class="{{ $rec->schedule_variance >= 0 ? 'good' : 'bad' }}">
                {{ number_format($rec->schedule_variance, 0, ',', '.') }}
            </td>
            <td class="{{ $rec->cost_variance >= 0 ? 'good' : 'bad' }}">
                {{ number_format($rec->cost_variance, 0, ',', '.') }}
            </td>
            <td class="{{ $rec->spi >= 1 ? 'good' : ($rec->spi >= 0.95 ? 'warn' : 'bad') }}">
                {{ number_format($rec->spi, 4) }}
            </td>
            <td class="{{ $rec->cpi >= 1 ? 'good' : ($rec->cpi >= 0.95 ? 'warn' : 'bad') }}">
                {{ number_format($rec->cpi, 4) }}
            </td>
            <td>{{ number_format($rec->eac, 0, ',', '.') }}</td>
            <td>{{ number_format($rec->etc, 0, ',', '.') }}</td>
            <td class="{{ $rec->vac >= 0 ? 'good' : 'bad' }}">
                {{ number_format($rec->vac, 0, ',', '.') }}
            </td>
            <td>{{ number_format($rec->tcpi, 4) }}</td>
            <td style="text-align:center;">
                {{ number_format($rec->physical_progress_percent, 2) }}%
            </td>
            <td style="text-align:center;">
                @if($rec->status_cost === 'over_budget')
                    <span class="badge-over">Overrun</span>
                @elseif($rec->status_cost === 'on_budget')
                    <span class="badge-on">On Budget</span>
                @else
                    <span class="badge-under">Under</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if($latest)
{{-- INTERPRETASI KINERJA --}}
<div class="section-title">Interpretasi Kinerja Proyek (Matriks CPI-SPI)</div>
@php
    $cpi = $latest->cpi;
    $spi = $latest->spi;
    if ($cpi > 1 && $spi > 1) {
        $zone = 'I'; $zoneClass = 'zone-i';
        $zoneDesc = 'Ahead of Schedule & Under Budget — Kondisi Ideal';
        $zoneColor = '#16a34a';
    } elseif ($cpi < 1 && $spi > 1) {
        $zone = 'II'; $zoneClass = 'zone-ii';
        $zoneDesc = 'Ahead of Schedule namun Over Budget';
        $zoneColor = '#ca8a04';
    } elseif ($cpi > 1 && $spi < 1) {
        $zone = 'III'; $zoneClass = 'zone-iii';
        $zoneDesc = 'Behind Schedule namun Under Budget';
        $zoneColor = '#ca8a04';
    } else {
        $zone = 'IV'; $zoneClass = 'zone-iv';
        $zoneDesc = 'Behind Schedule & Over Budget — Kondisi Kritis';
        $zoneColor = '#dc2626';
    }
@endphp
<div class="interpretasi">
    <div class="zone-box {{ $zoneClass }}">
        <div class="zone-title" style="color:{{ $zoneColor }}">
            Zone {{ $zone }}: {{ $zoneDesc }}
        </div>
        <div class="zone-detail">
            CPI = {{ number_format($cpi, 4) }} &nbsp;|&nbsp;
            SPI = {{ number_format($spi, 4) }} &nbsp;|&nbsp;
            TCPI = {{ number_format($latest->tcpi, 4) }} &nbsp;|&nbsp;
            EAC = Rp {{ number_format($latest->eac, 0, ',', '.') }} &nbsp;|&nbsp;
            VAC = Rp {{ number_format($latest->vac, 0, ',', '.') }}
        </div>
    </div>
</div>

{{-- FORMULA REFERENSI --}}
<div class="section-title">Referensi Formula EVA (PMI PMBOK)</div>
<table class="formula-table">
    <thead>
        <tr>
            <th width="8%">Metrik</th>
            <th width="25%">Formula</th>
            <th width="35%">Interpretasi Hasil</th>
            <th width="32%">Nilai Proyek Ini</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><b>CV</b></td>
            <td>EV - AC</td>
            <td>&gt; 0: Under Budget | &lt; 0: Cost Overrun</td>
            <td class="{{ $latest->cost_variance >= 0 ? 'good' : 'bad' }}">
                Rp {{ number_format($latest->cost_variance, 0, ',', '.') }}
                ({{ $latest->cost_variance >= 0 ? 'Under Budget' : 'Cost Overrun' }})
            </td>
        </tr>
        <tr>
            <td><b>SV</b></td>
            <td>EV - PV</td>
            <td>&gt; 0: On Schedule | &lt; 0: Behind Schedule</td>
            <td class="{{ $latest->schedule_variance >= 0 ? 'good' : 'bad' }}">
                Rp {{ number_format($latest->schedule_variance, 0, ',', '.') }}
                ({{ $latest->schedule_variance >= 0 ? 'On Schedule' : 'Behind Schedule' }})
            </td>
        </tr>
        <tr>
            <td><b>CPI</b></td>
            <td>EV / AC</td>
            <td>&gt; 1: Efisien | = 1: Sesuai | &lt; 1: Boros</td>
            <td class="{{ $latest->cpi >= 1 ? 'good' : 'bad' }}">
                {{ number_format($latest->cpi, 4) }}
                ({{ $latest->cpi >= 1 ? 'Efisien' : 'Boros' }})
            </td>
        </tr>
        <tr>
            <td><b>SPI</b></td>
            <td>EV / PV</td>
            <td>&gt; 1: Lebih Cepat | = 1: Tepat | &lt; 1: Terlambat</td>
            <td class="{{ $latest->spi >= 1 ? 'good' : 'bad' }}">
                {{ number_format($latest->spi, 4) }}
                ({{ $latest->spi >= 1 ? 'Lebih Cepat' : 'Terlambat' }})
            </td>
        </tr>
        <tr>
            <td><b>EAC</b></td>
            <td>BAC / CPI</td>
            <td>Estimasi total biaya akhir proyek</td>
            <td>Rp {{ number_format($latest->eac, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><b>ETC</b></td>
            <td>EAC - AC</td>
            <td>Estimasi biaya yang masih diperlukan</td>
            <td>Rp {{ number_format($latest->etc, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td><b>VAC</b></td>
            <td>BAC - EAC</td>
            <td>&gt; 0: Surplus | &lt; 0: Defisit</td>
            <td class="{{ $latest->vac >= 0 ? 'good' : 'bad' }}">
                Rp {{ number_format($latest->vac, 0, ',', '.') }}
                ({{ $latest->vac >= 0 ? 'Surplus' : 'Defisit' }})
            </td>
        </tr>
        <tr>
            <td><b>TCPI</b></td>
            <td>(BAC-EV)/(BAC-AC)</td>
            <td>&gt; 1: Harus lebih efisien | &lt; 1: Mudah tercapai</td>
            <td>{{ number_format($latest->tcpi, 4) }}</td>
        </tr>
    </tbody>
</table>
@endif

<div class="footer">
    Laporan ini digenerate otomatis oleh EVA Monitor System &mdash;
    Dicetak: {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
