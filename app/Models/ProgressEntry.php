<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ProgressEntry extends Model {
    protected $fillable = ['project_id','boq_sub_item_id','reported_by','period_label','report_date','physical_progress','actual_cost','earned_value','planned_value','attachment_path','notes'];
    protected $casts    = ['report_date'=>'date','actual_cost'=>'decimal:2','earned_value'=>'decimal:2'];

    public function subItem()  { return $this->belongsTo(BOQSubItem::class, 'boq_sub_item_id'); }
    public function reporter() { return $this->belongsTo(User::class, 'reported_by'); }
    public function project()  { return $this->belongsTo(Project::class); }

    public function scopePeriod($query, string $period) { return $query->where('period_label', $period); }
}
