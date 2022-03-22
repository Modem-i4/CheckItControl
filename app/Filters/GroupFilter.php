<?php


namespace App\Filters;


class GroupFilter extends QueryFilter
{
    protected $searchable = [
        'base.id',
        'base.title',
        'base.students',
    ];

    protected $sortable = [
        'base.id',
        'base.title',
        'total_lessons'
    ];
}
