<?php


namespace App\Filters;


class SchoolFilter extends QueryFilter
{
    protected $searchable = [
        'base.id',
        'base.title',
        'base.teachers',
    ];

    protected $sortable = [
        'base.id',
        'base.title',
    ];
}
