<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionReport extends Model
{
    protected $fillable = [
        'user_id',
        'net_total',
        'water_balance',
        'session_start',
        'session_end',
        'created_date'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


        public function expenses()
    {
        return $this->hasMany(Expense::class,'session_report_id');
    }

         public function subscriptions()
    {
        return $this->hasMany(Subscription::class,'session_report_id');
    }
}