<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    protected $casts = [
    'vector' => 'array'
];
    protected $fillable = ['name', 'phone' , 'vector'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

        public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }
}