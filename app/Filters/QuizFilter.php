<?php


namespace App\Filters;


class QuizFilter extends QueryFilter
{
    protected $searchable = [
        'base.id',
        'base.title',
        'base.description',
    ];

    protected $sortable = [
        'base.id',
        'base.title',
        'total_lessons'
    ];
}
