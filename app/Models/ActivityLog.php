<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model {
    public $timestamps = false;
    protected $fillable = ['user_id','project_id','module','action','description','old_values','new_values','ip_address','user_agent','created_at'];
    protected $casts    = ['old_values'=>'array','new_values'=>'array','created_at'=>'datetime'];

    public function user()    { return $this->belongsTo(User::class); }
    public function project() { return $this->belongsTo(Project::class); }
}
