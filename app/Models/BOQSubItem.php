<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class BOQSubItem extends Model {
    protected $table = 'boq_sub_items';
    protected $fillable = ['boq_item_id','project_id','code','name','specification','volume','unit','unit_price','total_price','weight_percent','planned_start','planned_end','order_no'];
    protected $casts = ['planned_start'=>'date','planned_end'=>'date','total_price'=>'decimal:2'];

    public function project()         { return $this->belongsTo(Project::class); }
    public function boqItem()         { return $this->belongsTo(BOQItem::class, 'boq_item_id'); }
    public function progressEntries() { return $this->hasMany(ProgressEntry::class); }

    public function getPlannedValueAt(Carbon $date): float {
        if ($date < $this->planned_start) return 0;
        if ($date >= $this->planned_end)  return (float) $this->total_price;
        $totalDays   = max($this->planned_start->diffInDays($this->planned_end), 1);
        $elapsedDays = $this->planned_start->diffInDays($date);
        return ($elapsedDays / $totalDays) * (float) $this->total_price;
    }

    public function getEarnedValue(): float {
        $progress = $this->progressEntries()->latest('report_date')->value('physical_progress') ?? 0;
        return ($progress / 100) * (float) $this->total_price;
    }
}
