<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Subscription;

class Subscriber extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'image',
        'zk_code'
    ];


    public function subscriptions()
{
    return $this->hasMany(Subscription::class);
}
}