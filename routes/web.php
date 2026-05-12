<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\ProjectController as AdminProject;
use App\Http\Controllers\PM\DashboardController as PMDashboard;
use App\Http\Controllers\PM\ProjectController as PMProject;
use App\Http\Controllers\PM\BOQController as PMBOQ;
use App\Http\Controllers\PM\ProgressController as PMProgress;
use App\Http\Controllers\PM\EVAController as PMEva;

Route::get('/', fn() => redirect()->route('login'));

Auth::routes(['register' => false, 'reset' => false]);

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::get('/projects', [AdminProject::class, 'index'])->name('projects.index');
});

/*
|--------------------------------------------------------------------------
| PM Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->prefix('pm')->name('pm.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [PMDashboard::class, 'index'])->name('dashboard');

    // Proyek
    Route::get('/projects', [PMProject::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [PMProject::class, 'create'])->name('projects.create');
    Route::post('/projects', [PMProject::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/edit', [PMProject::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{project}', [PMProject::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [PMProject::class, 'destroy'])->name('projects.destroy');

    // BOQ
    Route::get('/boq', [PMBOQ::class, 'index'])->name('boq.index');
    Route::get('/boq/{project}/items/create', [PMBOQ::class, 'createItem'])->name('boq.items.create');
    Route::post('/boq/{project}/items', [PMBOQ::class, 'storeItem'])->name('boq.items.store');
    Route::delete('/boq/items/{item}', [PMBOQ::class, 'destroyItem'])->name('boq.items.destroy');
    Route::get('/boq/items/{item}/sub/create', [PMBOQ::class, 'createSub'])->name('boq.sub.create');
    Route::post('/boq/items/{item}/sub', [PMBOQ::class, 'storeSub'])->name('boq.sub.store');
    Route::get('/boq/sub/{sub}/edit', [PMBOQ::class, 'editSub'])->name('boq.sub.edit');
    Route::put('/boq/sub/{sub}', [PMBOQ::class, 'updateSub'])->name('boq.sub.update');
    Route::delete('/boq/sub/{sub}', [PMBOQ::class, 'destroySub'])->name('boq.sub.destroy');

    // Progress
    Route::get('/progress', [PMProgress::class, 'index'])->name('progress.index');
    Route::get('/progress/create', [PMProgress::class, 'create'])->name('progress.create');
    Route::post('/progress', [PMProgress::class, 'store'])->name('progress.store');
    Route::get('/progress/{entry}/edit', [PMProgress::class, 'edit'])->name('progress.edit');
    Route::put('/progress/{entry}', [PMProgress::class, 'update'])->name('progress.update');
    Route::delete('/progress/period', [PMProgress::class, 'destroyPeriod'])->name('progress.destroyPeriod');
    Route::delete('/progress/{entry}', [PMProgress::class, 'destroy'])->name('progress.destroy');

    // EVA
    Route::post('/eva/calculate', [PMEva::class, 'calculate'])->name('eva.calculate');
    Route::get('/eva/{project}', [PMEva::class, 'show'])->name('eva.show');
    Route::get('/eva/{project}/export/pdf', [PMEva::class, 'exportPdf'])->name('eva.export.pdf');
    Route::get('/eva/{project}/export/excel', [PMEva::class, 'exportExcel'])->name('eva.export.excel');
});

/*
|--------------------------------------------------------------------------
| Redirect setelah login
|--------------------------------------------------------------------------
*/
Route::get('/home', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    return $user->isAdmin()
        ? redirect('/admin/dashboard')
        : redirect('/pm/dashboard');
})->middleware('auth')->name('home');
