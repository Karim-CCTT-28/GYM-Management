<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{  use SoftDeletes;
    protected $fillable = ['user_name', 'role', 'hashed_password', 'vector', 'isDeleted'];

    protected $casts = [
        'vector' => 'array',
    ];
    public function sessionReports(): HasMany
    {
        return $this->hasMany(SessionReport::class);
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

}