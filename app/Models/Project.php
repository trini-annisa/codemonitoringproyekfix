<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model {
    use SoftDeletes;
    protected $fillable = ['project_code','name','description','location','owner','contract_value','bac','start_date','end_date','status','contract_number','pm_id','created_by'];
    protected $casts    = ['start_date'=>'date','end_date'=>'date','contract_value'=>'decimal:2','bac'=>'decimal:2'];

    public function pm()          { return $this->belongsTo(User::class, 'pm_id'); }
    public function creator()     { return $this->belongsTo(User::class, 'created_by'); }
    public function boqItems()    { return $this->hasMany(BOQItem::class)->orderBy('order_no'); }
    public function evaRecords()  { return $this->hasMany(EVARecord::class)->orderBy('report_date'); }
    public function progressEntries() { return $this->hasMany(ProgressEntry::class); }
    public function activityLogs()    { return $this->hasMany(ActivityLog::class); }
}
