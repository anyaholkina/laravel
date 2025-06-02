<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChangeLog extends Model
{
   protected $fillable = [
    'entity', 'entity_id', 'before', 'after', 'action', 'user_id'
];

protected $casts = [
    'before' => 'array',
    'after' => 'array',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}