<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'session_report_id',
        'recipient',
        'clause',
        'amount'
    ];

    public function sessionReport()
    {
        return $this->belongsTo(SessionReport::class);
    }
}