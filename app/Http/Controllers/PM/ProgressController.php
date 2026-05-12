<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\BOQSubItem;
use App\Models\ProgressEntry;
use App\Services\EVACalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgressController extends Controller
{
public function index()
{
    $projects = Project::where('pm_id', Auth::id())
                       ->with(['progressEntries'])
                       ->latest()
                       ->get();

    // Kelompokkan progress per proyek per periode
    $progressByProject = [];
    foreach ($projects as $project) {
        $grouped = $project->progressEntries
            ->groupBy('period_label')
            ->map(function ($entries, $periodLabel) {
                return [
                    'period_label'      => $periodLabel,
                    'report_date'       => $entries->first()->report_date,
                    'total_ev'          => $entries->sum('earned_value'),
                    'total_ac'          => $entries->sum('actual_cost'),
                    'total_pv'          => $entries->sum('planned_value'),
                    'avg_progress'      => $entries->avg('physical_progress'),
                    'entry_count'       => $entries->count(),
                    'entries'           => $entries,
                ];
            })
            ->sortByDesc('report_date')
            ->values();

        $progressByProject[$project->id] = $grouped;
    }

    return view('pm.progress.index', compact('projects', 'progressByProject'));
}

    public function create(Request $request)
    {
        $projects = Project::where('pm_id', Auth::id())
                           ->with(['boqItems.subItems'])
                           ->get();

        $selectedProject = null;
        $subItems        = collect();

        if ($request->project_id) {
            $selectedProject = $projects->firstWhere('id', $request->project_id);
            if ($selectedProject) {
                $subItems = BOQSubItem::where('project_id', $selectedProject->id)
                                     ->with('boqItem')
                                     ->orderBy('order_no')
                                     ->get();
            }
        }

        return view('pm.progress.create', compact('projects', 'selectedProject', 'subItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id'      => 'required|exists:projects,id',
            'period_label'    => 'required|string|max:10',
            'report_date'     => 'required|date',
            'entries'         => 'required|array|min:1',
            'entries.*.boq_sub_item_id' => 'required|exists:boq_sub_items,id',
            'entries.*.physical_progress' => 'required|numeric|min:0|max:100',
            'entries.*.actual_cost'       => 'required|numeric|min:0',
            'entries.*.notes'             => 'nullable|string',
        ]);

        $project     = Project::findOrFail($request->project_id);
        $reportDate  = Carbon::parse($request->report_date);
        $periodLabel = $request->period_label;

        foreach ($request->entries as $entry) {
            $subItem = BOQSubItem::find($entry['boq_sub_item_id']);
            $ev      = ($entry['physical_progress'] / 100) * (float) $subItem->total_price;
            $pv      = $subItem->getPlannedValueAt($reportDate);

            ProgressEntry::updateOrCreate(
                [
                    'boq_sub_item_id' => $entry['boq_sub_item_id'],
                    'period_label'    => $periodLabel,
                ],
                [
                    'project_id'       => $project->id,
                    'reported_by'      => Auth::id(),
                    'report_date'      => $reportDate,
                    'physical_progress'=> $entry['physical_progress'],
                    'actual_cost'      => $entry['actual_cost'],
                    'earned_value'     => $ev,
                    'planned_value'    => $pv,
                    'notes'            => $entry['notes'] ?? null,
                ]
            );
        }

        // Hitung EVA otomatis setelah input progress
        $evaService = new EVACalculationService();
        $evaService->calculate($project, $periodLabel, $reportDate);

        return redirect()->route('pm.progress.index')
                         ->with('success', "Progress periode {$periodLabel} berhasil disimpan & EVA dihitung otomatis!");
    }

    public function edit(ProgressEntry $entry)
    {
        abort_if($entry->project->pm_id !== Auth::id(), 403);
        $subItem = $entry->subItem;
        $project = $entry->project;
        return view('pm.progress.edit', compact('entry', 'subItem', 'project'));
    }

    public function update(Request $request, ProgressEntry $entry)
    {
        abort_if($entry->project->pm_id !== Auth::id(), 403);

        $request->validate([
            'physical_progress' => 'required|numeric|min:0|max:100',
            'actual_cost'       => 'required|numeric|min:0',
            'notes'             => 'nullable|string',
        ]);

        $subItem    = $entry->subItem;
        $reportDate = $entry->report_date;
        $ev         = ($request->physical_progress / 100) * (float) $subItem->total_price;
        $pv         = $subItem->getPlannedValueAt($reportDate);

        $entry->update([
            'physical_progress' => $request->physical_progress,
            'actual_cost'       => $request->actual_cost,
            'earned_value'      => $ev,
            'planned_value'     => $pv,
            'notes'             => $request->notes,
        ]);

        // Hitung ulang EVA
        $evaService = new EVACalculationService();
        $evaService->calculate($entry->project, $entry->period_label, $reportDate);

        return redirect()->route('pm.progress.index')
                         ->with('success', 'Progress berhasil diperbarui & EVA dihitung ulang!');
    }

    public function destroy(ProgressEntry $entry)
    {
        abort_if($entry->project->pm_id !== Auth::id(), 403);
        $project     = $entry->project;
        $periodLabel = $entry->period_label;
        $reportDate  = $entry->report_date;
        $entry->delete();

        // Hitung ulang EVA setelah hapus
        $evaService = new EVACalculationService();
        $evaService->calculate($project, $periodLabel, Carbon::parse($reportDate));

        return redirect()->route('pm.progress.index')
                         ->with('success', 'Progress berhasil dihapus!');
    }

    public function destroyPeriod(Request $request)
{
    $request->validate([
        'project_id'   => 'required|exists:projects,id',
        'period_label' => 'required|string',
    ]);

    $project = Project::findOrFail($request->project_id);
    abort_if($project->pm_id !== Auth::id(), 403);

    ProgressEntry::where('project_id', $request->project_id)
                 ->where('period_label', $request->period_label)
                 ->delete();

    // Hapus EVA record periode ini juga
    \App\Models\EVARecord::where('project_id', $request->project_id)
                         ->where('period_label', $request->period_label)
                         ->delete();

    return redirect()->route('pm.progress.index')
                     ->with('success', "Data periode {$request->period_label} berhasil dihapus.");
}
}
