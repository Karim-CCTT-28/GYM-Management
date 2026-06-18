<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckIn extends Model
{
    //

    protected $fillable = ['subscriber_id' , 'check_in_date' , 'is_allow'];
     public function subscriber()
    {
        return $this->belongsTo(Subscriber::class);
    }
}
