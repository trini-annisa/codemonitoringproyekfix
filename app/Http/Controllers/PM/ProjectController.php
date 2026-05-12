<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::where('pm_id', Auth::id())
                           ->latest()
                           ->get();
        return view('pm.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('pm.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'contract_number'=> 'nullable|string|max:100',
            'owner'          => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'contract_value' => 'required|numeric|min:0',
            'bac'            => 'required|numeric|min:0',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
        ]);

        $year  = date('Y');
        $count = Project::whereYear('created_at', $year)->count() + 1;
        $code  = 'PRJ-' . $year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        Project::create([
            'project_code'   => $code,
            'name'           => $request->name,
            'description'    => $request->description,
            'contract_number'=> $request->contract_number,
            'owner'          => $request->owner,
            'location'       => $request->location,
            'contract_value' => $request->contract_value,
            'bac'            => $request->bac,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'status'         => 'active',
            'pm_id'          => Auth::id(),
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('pm.projects.index')
                         ->with('success', 'Proyek berhasil dibuat!');
    }

    public function edit(Project $project)
    {
        abort_if($project->pm_id !== Auth::id(), 403);
        return view('pm.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        abort_if($project->pm_id !== Auth::id(), 403);

        $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'contract_number'=> 'nullable|string|max:100',
            'owner'          => 'required|string|max:255',
            'location'       => 'required|string|max:255',
            'contract_value' => 'required|numeric|min:0',
            'bac'            => 'required|numeric|min:0',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after:start_date',
            'status'         => 'required|in:active,completed,on_hold',
        ]);

        $project->update([
            'name'           => $request->name,
            'description'    => $request->description,
            'contract_number'=> $request->contract_number,
            'owner'          => $request->owner,
            'location'       => $request->location,
            'contract_value' => $request->contract_value,
            'bac'            => $request->bac,
            'start_date'     => $request->start_date,
            'end_date'       => $request->end_date,
            'status'         => $request->status,
        ]);

        return redirect()->route('pm.projects.index')
                         ->with('success', 'Proyek berhasil diperbarui!');
    }

    public function destroy(Project $project)
    {
        abort_if($project->pm_id !== Auth::id(), 403);
        $project->delete();

        return redirect()->route('pm.projects.index')
                         ->with('success', 'Proyek berhasil dihapus!');
    }
}
