<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\EVARecord;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProjects      = Project::count();
        $activeProjects     = Project::where('status', 'active')->count();
        $totalPM            = User::where('role', 'project_manager')->count();
        $totalContractValue = Project::sum('contract_value');

        $projects = Project::with(['pm', 'evaRecords'])
                           ->latest()
                           ->take(10)
                           ->get();

        $recentLogs = ActivityLog::with('user')
                                 ->latest()
                                 ->take(10)
                                 ->get();

        $latestEvas = EVARecord::latest()->get();

        $evaGlobal = [
            'avg_cpi'  => $latestEvas->avg('cpi') ?? 0,
            'avg_spi'  => $latestEvas->avg('spi') ?? 0,
            'total_cv' => $latestEvas->sum('cost_variance') ?? 0,
            'total_sv' => $latestEvas->sum('schedule_variance') ?? 0,
        ];

        return view('admin.dashboard', compact(
            'totalProjects',
            'activeProjects',
            'totalPM',
            'totalContractValue',
            'projects',
            'recentLogs',
            'evaGlobal'
        ));
    }
}
