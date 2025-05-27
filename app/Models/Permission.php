<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ChangeLog;
use Illuminate\Support\Facades\Auth;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'cipher', 'description'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'roles_and_permissions');
    }
}