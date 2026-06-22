<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscriber extends Model
{  use SoftDeletes;
    protected $casts = [
    'vector' => 'array'
];
    protected $fillable = ['name', 'phone' , 'vector' , 'isDeleted'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

        public function checkIns()
    {
        return $this->hasMany(CheckIn::class);
    }
}