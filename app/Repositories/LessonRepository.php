<?php


namespace App\Repositories;


use App\Models\Lesson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonRepository
{
    function startConditions()
    {
        return app(Lesson::class);
    }
    public function getList()
    {
        $perPage = request('perPage');
        $columns = [
            'base.id',
            'base.code',
            'base.date',
            'quizzes.title as quiz_title',
            'base.group_id',
            'groups.title as group',
        ];
        $userId = Auth::id();
        return $this->startConditions()
            ->from('lessons as base')
            ->select($columns)
            ->leftJoin('quizzes','base.quiz_id','=','quizzes.id')
            ->leftJoin('groups','base.group_id','=','groups.id')
            ->filter()
            ->where('base.teacher_id', $userId)
            ->paginate($perPage);
    }

    public function get($id) {
        $columns = [
            'base.id',
            'base.code',
            'base.date',
            'base.quiz_id',
            'base.group_id',
            'quizzes.title as quiz_title',
            'quizzes.subject_id',
            'groups.title as group_title',
            'groups.studentsJSON as students',
        ];

        return $this->startConditions()
            ->from('lessons as base')
            ->select($columns)
            ->leftJoin('quizzes','base.quiz_id','=','quizzes.id')
            ->leftJoin('groups','base.group_id','=','groups.id')
            ->where('base.id', $id)
            ->first();
    }

    public function add() {
        $quizId = request('data.quiz_id');
        $groupId =  request('data.group_id');
        $code =  request('data.code');
        $userId = Auth::id();
        return $this->startConditions()
            ->insertGetId([
                'quiz_id' => $quizId,
                'group_id' => $groupId,
                'code' => $code,
                'teacher_id' => $userId,
            ]);
    }
    public function remove($id)
    {
        return $this->startConditions()
            ->where('id', $id)
            ->delete();
    }
    public function setQuizStats($id, $data) {
        return $this->startConditions()
            ->where('id', $id)
            ->update(['QuizStats' => $data]);
    }
    public function setDartsStats($id, $data) {
        return $this->startConditions()
            ->where('id', $id)
            ->update(['DartsStats' => $data]);
    }
    public function setStudentsStats($id, $data) {
        return $this->startConditions()
            ->where('id', $id)
            ->update(['StudentsStats' => $data]);
    }
    public function getQuizStats($id) {

        $columns = [
            'QuizStats',
            'DartsStats',
            'StudentsStats',
        ];

        return $this->startConditions()
            ->select($columns)
            ->where('id', $id)
            ->first();
    }
    public function testStats($id) {
        return $this->startConditions()
            ->where([
                ['id', $id],
                ['QuizStats', '<>', null],
                ['DartsStats', '<>', null],
            ])
            ->count();
    }
}
