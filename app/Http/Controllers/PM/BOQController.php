<?php

namespace App\Http\Controllers\PM;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\BOQItem;
use App\Models\BOQSubItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BOQController extends Controller
{
    // Tampilkan daftar proyek untuk pilih BOQ
    public function index()
    {
        $projects = Project::where('pm_id', Auth::id())
                           ->with(['boqItems.subItems'])
                           ->latest()
                           ->get();

        return view('pm.boq.index', compact('projects'));
    }

    // Form tambah item BOQ (kelompok pekerjaan)
    public function createItem(Project $project)
    {
        abort_if($project->pm_id !== Auth::id(), 403);
        return view('pm.boq.create-item', compact('project'));
    }

    // Simpan item BOQ
    public function storeItem(Request $request, Project $project)
    {
        abort_if($project->pm_id !== Auth::id(), 403);

        $request->validate([
            'code'    => 'required|string|max:20',
            'name'    => 'required|string|max:255',
            'order_no'=> 'required|integer|min:1',
        ]);

        BOQItem::create([
            'project_id' => $project->id,
            'code'       => strtoupper($request->code),
            'name'       => $request->name,
            'order_no'   => $request->order_no,
            'subtotal'   => 0,
            'weight_percent' => 0,
        ]);

        return redirect()->route('pm.boq.index')
                         ->with('success', 'Kelompok pekerjaan berhasil ditambahkan!');
    }

    // Form tambah sub-item BOQ (item pekerjaan rinci)
    public function createSub(BOQItem $item)
    {
        abort_if($item->project->pm_id !== Auth::id(), 403);
        $project = $item->project;
        return view('pm.boq.create-sub', compact('item', 'project'));
    }

    // Simpan sub-item BOQ + recalculate subtotal & weight
    public function storeSub(Request $request, BOQItem $item)
    {
        abort_if($item->project->pm_id !== Auth::id(), 403);

        $request->validate([
            'code'         => 'required|string|max:30',
            'name'         => 'required|string|max:255',
            'specification'=> 'nullable|string',
            'volume'       => 'required|numeric|min:0.001',
            'unit'         => 'required|string|max:20',
            'unit_price'   => 'required|numeric|min:0',
            'planned_start'=> 'required|date',
            'planned_end'  => 'required|date|after:planned_start',
            'order_no'     => 'required|integer|min:1',
        ]);

        $totalPrice = $request->volume * $request->unit_price;

        BOQSubItem::create([
            'boq_item_id'   => $item->id,
            'project_id'    => $item->project_id,
            'code'          => $request->code,
            'name'          => $request->name,
            'specification' => $request->specification,
            'volume'        => $request->volume,
            'unit'          => $request->unit,
            'unit_price'    => $request->unit_price,
            'total_price'   => $totalPrice,
            'planned_start' => $request->planned_start,
            'planned_end'   => $request->planned_end,
            'order_no'      => $request->order_no,
            'weight_percent'=> 0,
        ]);

        // Recalculate subtotal item & weight semua sub-item
        $this->recalculateWeights($item->project_id);

        return redirect()->route('pm.boq.index')
                         ->with('success', 'Item pekerjaan berhasil ditambahkan!');
    }

    public function editSub(BOQSubItem $sub)
{
    abort_if($sub->project->pm_id !== Auth::id(), 403);
    $item    = $sub->boqItem;
    $project = $sub->project;
    return view('pm.boq.edit-sub', compact('sub', 'item', 'project'));
}

public function updateSub(Request $request, BOQSubItem $sub)
{
    abort_if($sub->project->pm_id !== Auth::id(), 403);

    $request->validate([
        'code'          => 'required|string|max:30',
        'name'          => 'required|string|max:255',
        'specification' => 'nullable|string',
        'volume'        => 'required|numeric|min:0.001',
        'unit'          => 'required|string|max:20',
        'unit_price'    => 'required|numeric|min:0',
        'planned_start' => 'required|date',
        'planned_end'   => 'required|date|after:planned_start',
        'order_no'      => 'required|integer|min:1',
    ]);

    $totalPrice = $request->volume * $request->unit_price;

    $sub->update([
        'code'          => $request->code,
        'name'          => $request->name,
        'specification' => $request->specification,
        'volume'        => $request->volume,
        'unit'          => $request->unit,
        'unit_price'    => $request->unit_price,
        'total_price'   => $totalPrice,
        'planned_start' => $request->planned_start,
        'planned_end'   => $request->planned_end,
        'order_no'      => $request->order_no,
    ]);

    $this->recalculateWeights($sub->project_id);

    return redirect()->route('pm.boq.index')
                     ->with('success', 'Item pekerjaan berhasil diperbarui!');
}
    // Hapus item BOQ beserta sub-itemnya
    public function destroyItem(BOQItem $item)
    {
        abort_if($item->project->pm_id !== Auth::id(), 403);
        $projectId = $item->project_id;
        $item->delete();
        $this->recalculateWeights($projectId);

        return redirect()->route('pm.boq.index')
                         ->with('success', 'Kelompok pekerjaan berhasil dihapus!');
    }

    // Hapus sub-item BOQ
    public function destroySub(BOQSubItem $sub)
    {
        abort_if($sub->project->pm_id !== Auth::id(), 403);
        $projectId = $sub->project_id;
        $itemId    = $sub->boq_item_id;
        $sub->delete();
        $this->recalculateWeights($projectId);

        return redirect()->route('pm.boq.index')
                         ->with('success', 'Item pekerjaan berhasil dihapus!');
    }

    // Helper: hitung ulang subtotal & weight_percent
    private function recalculateWeights(int $projectId): void
    {
        $project   = Project::find($projectId);
        $totalBAC  = (float) $project->bac ?: 1;

        $items = BOQItem::where('project_id', $projectId)
                        ->with('subItems')
                        ->get();

        foreach ($items as $item) {
            $subtotal = $item->subItems->sum('total_price');

            // Update subtotal & weight tiap item
            $item->update([
                'subtotal'       => $subtotal,
                'weight_percent' => round(($subtotal / $totalBAC) * 100, 4),
            ]);

            // Update weight tiap sub-item
            foreach ($item->subItems as $sub) {
                $sub->update([
                    'weight_percent' => round(((float)$sub->total_price / $totalBAC) * 100, 4),
                ]);
            }
        }
    }
}
