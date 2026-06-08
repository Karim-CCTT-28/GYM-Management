<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    //

     protected $fillable = ["created_by","end_date" , "start_date" , "session_id" , "subscriber_id"];

    public function subscriber()
{
    return $this->belongsTo(Subscriber::class);
}
}
