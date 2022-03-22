<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class School extends Model
{
    use HasFactory, Filterable;
    protected $casts = [
        'teachers' => 'array',
    ];
    public $timestamps = false;
}
