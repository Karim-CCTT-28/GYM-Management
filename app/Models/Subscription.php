<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = [
        'subscriber_id', 
        'subscription_type_id', 
        'start_date', 
        'end_date', 
        'created_by'
        ,'session_report_id'
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    public function subscriptionType(): BelongsTo
    {
        return $this->belongsTo(SubscriptionType::class);
    }


     public function sessionReport()
    {
        return $this->belongsTo(SessionReport::class);
    }



}