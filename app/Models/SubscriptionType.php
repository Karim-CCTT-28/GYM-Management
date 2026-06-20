<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionType extends Model
{
    protected $fillable = ['duration', 'price' , 'duration_unit' , 'isDeleted'];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}