<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Lesson extends Model
{
    use HasFactory, Filterable;
    protected $casts = [
        'students' => 'array',
    ];
    public $timestamps = false;
}
