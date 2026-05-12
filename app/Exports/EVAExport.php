<?php

namespace App\Exports;

use App\Models\EVARecord;
use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class EVAExport implements FromCollection, WithHeadings, WithTitle,
                           WithStyles, WithColumnWidths, WithColumnFormatting
{
    protected Project $project;

    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function collection()
    {
        return EVARecord::where('project_id', $this->project->id)
            ->orderBy('report_date')
            ->get()
            ->map(fn($r) => [
                $r->period_label,
                $r->report_date->format('d/m/Y'),
                (float) $r->bac,
                (float) $r->planned_value,
                (float) $r->earned_value,
                (float) $r->actual_cost,
                (float) $r->schedule_variance,
                (float) $r->cost_variance,
                (float) $r->spi,
                (float) $r->cpi,
                (float) $r->eac,
                (float) $r->etc,
                (float) $r->vac,
                (float) $r->tcpi,
                (float) $r->physical_progress_percent,
                (float) $r->planned_progress_percent,
                $r->status_cost,
                $r->status_schedule,
            ]);
    }

    public function headings(): array
    {
        return [
            'Periode', 'Tanggal',
            'BAC (Rp)', 'PV (Rp)', 'EV (Rp)', 'AC (Rp)',
            'SV (Rp)', 'CV (Rp)',
            'SPI', 'CPI',
            'EAC (Rp)', 'ETC (Rp)', 'VAC (Rp)', 'TCPI',
            'Progress Fisik (%)', 'Progress Rencana (%)',
            'Status Biaya', 'Status Jadwal',
        ];
    }

    public function title(): string
    {
        return 'EVA ' . $this->project->project_code;
    }

    public function columnFormats(): array
    {
        $rupiah = '#,##0';
        $desimal = '0.0000';
        $persen = '0.00"%"';

        return [
            'C' => $rupiah,  // BAC
            'D' => $rupiah,  // PV
            'E' => $rupiah,  // EV
            'F' => $rupiah,  // AC
            'G' => $rupiah,  // SV
            'H' => $rupiah,  // CV
            'I' => $desimal, // SPI
            'J' => $desimal, // CPI
            'K' => $rupiah,  // EAC
            'L' => $rupiah,  // ETC
            'M' => $rupiah,  // VAC
            'N' => $desimal, // TCPI
            'O' => $persen,  // Progress Fisik
            'P' => $persen,  // Progress Rencana
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Periode
            'B' => 14,  // Tanggal
            'C' => 20,  // BAC
            'D' => 20,  // PV
            'E' => 20,  // EV
            'F' => 20,  // AC
            'G' => 20,  // SV
            'H' => 20,  // CV
            'I' => 10,  // SPI
            'J' => 10,  // CPI
            'K' => 20,  // EAC
            'L' => 20,  // ETC
            'M' => 20,  // VAC
            'N' => 10,  // TCPI
            'O' => 18,  // Progress Fisik
            'P' => 18,  // Progress Rencana
            'Q' => 16,  // Status Biaya
            'R' => 16,  // Status Jadwal
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();

        // Style header
        $sheet->getStyle('A1:R1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => 'solid',
                'startColor' => ['rgb' => '1E3A5F'],
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical'   => 'center',
                'wrapText'   => true,
            ],
        ]);

        // Freeze header row
        $sheet->freezePane('A2');

        // Border semua sel
        $sheet->getStyle('A1:R' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => 'thin',
                    'color'       => ['rgb' => 'D1D5DB'],
                ],
            ],
        ]);

        // Warna alternating rows
        for ($row = 2; $row <= $lastRow; $row++) {
            if ($row % 2 === 0) {
                $sheet->getStyle('A' . $row . ':R' . $row)->applyFromArray([
                    'fill' => [
                        'fillType'   => 'solid',
                        'startColor' => ['rgb' => 'F8FAFC'],
                    ],
                ]);
            }
        }

        // Tinggi header row
        $sheet->getRowDimension(1)->setRowHeight(35);

        return [];
    }
}
