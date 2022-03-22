<?php


namespace App\Repositories;


use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class SubjectRepository
{
    function startConditions()
    {
        return app(Subject::class);
    }
    public function getList()
    {
        $columns = [
            'base.id',
            'base.title',
            //DB::raw('COUNT(quizzes.id) as total_quizzes')
        ];
        return $this->startConditions()
            ->from('subjects as base')
            ->select($columns)
            //->join('quizzes', 'base.id', '=', 'quizzes.quiz_id')
            ->get();
    }
}
