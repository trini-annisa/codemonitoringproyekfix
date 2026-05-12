<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\EVARecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();


$myProjects = Project::where('pm_id', $user->id)
                             ->latest()
                             ->get();

        $selectedProject = null;
        $latestEva       = null;
        $evaHistory      = collect();

        if ($myProjects->isNotEmpty()) {
            $projectId       = $request->get('project_id', $myProjects->first()->id);
            $selectedProject = $myProjects->firstWhere('id', $projectId);

            if ($selectedProject) {
                $latestEva  = EVARecord::where('project_id', $selectedProject->id)
                                       ->latest('report_date')
                                       ->first();
                $evaHistory = EVARecord::where('project_id', $selectedProject->id)
                                       ->orderBy('report_date')
                                       ->get();
            }
        }

        return view('pm.dashboard', compact(
            'myProjects',
            'selectedProject',
            'latestEva',
            'evaHistory'
        ));
    }
}
