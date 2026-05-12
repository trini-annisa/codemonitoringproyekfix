<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BOQItem extends Model {
    protected $table = 'boq_items';
    protected $fillable = ['project_id','code','name','subtotal','weight_percent','order_no'];

    public function project()  { return $this->belongsTo(Project::class); }
    public function subItems() { return $this->hasMany(BOQSubItem::class, 'boq_item_id')->orderBy('order_no'); }
}
