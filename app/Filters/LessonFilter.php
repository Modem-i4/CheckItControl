<?php


namespace App\Filters;


class LessonFilter extends QueryFilter
{
    protected $searchable = [
        'id',
        'code',
        'title',
        'date',
        'group',
    ];

    protected $sortable = [
        'id',
        'code',
        'title',
        'date',
        'group',
        'quiz_id',

    ];
}
