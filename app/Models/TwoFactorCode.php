<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class TwoFactorCode extends Model
{
     protected $table = 'two_factor_codes';

    protected $fillable = [
        'user_id', 'client_identifier', 'code', 'expires_at', 'request_count',
    ];

    protected $dates = ['expires_at'];

    public function isExpired()
    {
        return $this->expires_at->isPast();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}