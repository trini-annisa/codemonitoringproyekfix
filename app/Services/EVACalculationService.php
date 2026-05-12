<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ProgressEntry;
use App\Models\EVARecord;
use App\Models\BOQSubItem;
use Carbon\Carbon;

class EVACalculationService
{
    public function calculate(Project $project, string $periodLabel, Carbon $reportDate): EVARecord
    {
        $bac = (float) $project->bac;

        // ── 1. PLANNED VALUE (PV) ──────────────────────────────────────
        // PV = jumlah nilai rencana semua sub-item pada tanggal laporan
        $subItems = BOQSubItem::where('project_id', $project->id)->get();
        $pv = $subItems->sum(fn($sub) => $sub->getPlannedValueAt($reportDate));

        // ── 2. EARNED VALUE (EV) ───────────────────────────────────────
        // EV = jumlah (% fisik realisasi × total_price) per sub-item
        $progressEntries = ProgressEntry::where('project_id', $project->id)
                                        ->where('period_label', $periodLabel)
                                        ->get();

        $ev = $progressEntries->sum('earned_value');
        $ac = $progressEntries->sum('actual_cost');

        // ── 3. VARIANCES ───────────────────────────────────────────────
        $cv = $ev - $ac;          // Cost Variance
        $sv = $ev - $pv;          // Schedule Variance

        // ── 4. INDICES ─────────────────────────────────────────────────
        $cpi = $ac > 0 ? round($ev / $ac, 4) : 0;
        $spi = $pv > 0 ? round($ev / $pv, 4) : 0;

        // ── 5. FORECASTS ───────────────────────────────────────────────
        $eac  = $cpi > 0 ? round($bac / $cpi, 2) : $bac;   // Estimate at Completion
        $etc  = round($eac - $ac, 2);                        // Estimate to Complete
        $vac  = round($bac - $eac, 2);                       // Variance at Completion
        $tcpi = ($bac - $ev) > 0
                    ? round(($bac - $ev) / ($bac - $ac), 4)
                    : 0;                                      // To-Complete Performance Index

        // ── 6. PROGRESS % ──────────────────────────────────────────────
        $physicalPct = $bac > 0 ? round(($ev / $bac) * 100, 2) : 0;
        $plannedPct  = $bac > 0 ? round(($pv / $bac) * 100, 2) : 0;

        // ── 7. STATUS ──────────────────────────────────────────────────
        $statusCost = match(true) {
            $cpi > 1.05 => 'under_budget',
            $cpi >= 0.95 => 'on_budget',
            default => 'over_budget',
        };

        $statusSchedule = match(true) {
            $spi > 1.05 => 'ahead_of_schedule',
            $spi >= 0.95 => 'on_schedule',
            default => 'behind_schedule',
        };

        // ── 8. SIMPAN / UPDATE EVA RECORD ──────────────────────────────
        return EVARecord::updateOrCreate(
            [
                'project_id'   => $project->id,
                'period_label' => $periodLabel,
            ],
            [
                'report_date'              => $reportDate,
                'bac'                      => $bac,
                'planned_value'            => round($pv, 2),
                'earned_value'             => round($ev, 2),
                'actual_cost'              => round($ac, 2),
                'cost_variance'            => round($cv, 2),
                'schedule_variance'        => round($sv, 2),
                'cpi'                      => $cpi,
                'spi'                      => $spi,
                'eac'                      => $eac,
                'etc'                      => $etc,
                'vac'                      => $vac,
                'tcpi'                     => $tcpi,
                'physical_progress_percent'=> $physicalPct,
                'planned_progress_percent' => $plannedPct,
                'status_cost'              => $statusCost,
                'status_schedule'          => $statusSchedule,
            ]
        );
    }
}
