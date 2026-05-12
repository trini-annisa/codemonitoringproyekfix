<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EVARecord extends Model {
    protected $table = 'eva_records'; // ← tambahkan baris ini

    protected $fillable = ['project_id','period_label','report_date','bac','planned_value','earned_value','actual_cost','cost_variance','schedule_variance','cpi','spi','eac','etc','vac','tcpi','physical_progress_percent','planned_progress_percent','status_cost','status_schedule','notes'];
    protected $casts    = ['report_date'=>'date','cpi'=>'decimal:4','spi'=>'decimal:4'];

    public function project() { return $this->belongsTo(Project::class); }
}
