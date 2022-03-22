<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Filterable;

class Quiz extends Model
{
    use HasFactory, Filterable;
    protected $casts = [
        'quizJSON' => 'array',
        'dartsJSON' => 'array',
        'favorite' => 'boolean'
    ];
    public $timestamps = false;
}
