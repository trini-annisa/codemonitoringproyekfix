<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\EVARecord;
use App\Services\EVACalculationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EVAExport;
use Carbon\Carbon;

class EVAController extends Controller
{
    public function calculate(Request $request)
    {
        $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'period_label' => 'required|string|max:10',
            'report_date'  => 'required|date',
        ]);

        $project = Project::findOrFail($request->project_id);

        // Ganti pm_id → user_id
        abort_if($project->pm_id !== Auth::id(), 403);

        $evaService = new EVACalculationService();
        $evaService->calculate(
            $project,
            $request->period_label,
            Carbon::parse($request->report_date)
        );

        return redirect()->route('pm.dashboard', ['project_id' => $project->id])
                         ->with('success', 'EVA berhasil dihitung!');
    }

    public function show(Project $project)
    {
        // Ganti pm_id → user_id
        abort_if($project->pm_id !== Auth::id(), 403);

        $evaRecords = EVARecord::where('project_id', $project->id)
                               ->orderBy('report_date')
                               ->get();

        return view('pm.eva.show', compact('project', 'evaRecords'));
    }

    public function exportPdf(Project $project)
    {
        // Ganti pm_id → user_id
       abort_if($project->pm_id !== Auth::id(), 403);

        $evaRecords = EVARecord::where('project_id', $project->id)
                               ->orderBy('report_date')
                               ->get();

        $latest = $evaRecords->last();

        $pdf = Pdf::loadView('pm.eva.pdf', compact('project', 'evaRecords', 'latest'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan-EVA-' . $project->project_code . '.pdf');
    }

    public function exportExcel(Project $project)
    {
        // Ganti pm_id → user_id
        abort_if($project->pm_id !== Auth::id(), 403);

        return Excel::download(
            new EVAExport($project),
            'Laporan-EVA-' . $project->project_code . '.xlsx'
        );
    }
}
