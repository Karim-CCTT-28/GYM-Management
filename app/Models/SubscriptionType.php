<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionType extends Model
{  use SoftDeletes;
    protected $fillable = ['duration', 'price' , 'duration_unit' , 'isDeleted'];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}