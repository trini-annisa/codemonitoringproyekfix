<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = ['name','email','password','role','is_active','phone'];
    protected $hidden   = ['password','remember_token'];
    protected $casts    = ['email_verified_at'=>'datetime','password'=>'hashed','is_active'=>'boolean'];

    public function isAdmin(): bool           { return $this->role === 'admin'; }
    public function isProjectManager(): bool  { return $this->role === 'project_manager'; }
    public function projects()                { return $this->hasMany(Project::class, 'pm_id'); }
    public function activityLogs()            { return $this->hasMany(ActivityLog::class); }
}
