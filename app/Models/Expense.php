<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
      use SoftDeletes;
    protected $fillable = [
        'session_report_id',
        'recipient',
        'clause',
        'amount',
        'isDeleted'
    ];

    public function sessionReport()
    {
        return $this->belongsTo(SessionReport::class);
    }
}