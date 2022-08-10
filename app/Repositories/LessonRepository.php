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

    public function add($quizId, $quizCount) {
        $groupId =  request('data.group_id');
        $code =  request('data.code');
        $userId = Auth::id();
        $postsStats = $this->generatePostsStats($quizCount['posts']);
        $dartsStats = $this->generateDartsStats($quizCount['darts']);
        return $this->startConditions()
            ->insertGetId([
                'quiz_id' => $quizId,
                'group_id' => $groupId,
                'code' => $code,
                'teacher_id' => $userId,
                'quizStats' => $postsStats,
                'dartsStats' => $dartsStats,
                'date' => now(),
            ]);
    }
    public function generatePostsStats($amount): string{
        $output = "[";
        $output .= str_repeat("[[0,0],[0,0],[0,0]],", $amount);
        $output = substr($output, 0, -1);
        $output .= "]";
        return $output;
    }
    public function generateDartsStats($amount): string {
        $output = "[";
        $output .= str_repeat("[[0],[0],[0]],", $amount);
        $output = substr($output, 0, -1);
        $output .= "]";
        return $output;
    }
    public function remove($id)
    {
        return $this->startConditions()
            ->where('id', $id)
            ->delete();
    }
    public function updPostsStats($id) {
        $postsStats = $this->getSingleStat($id, 'QuizStats');
        $postsStats[request('x')][request('y')][request('z')] += 1;
        return $this->startConditions()
            ->where('id', $id)
            ->update(['QuizStats' => $postsStats]);
    }
    public function updDartsStats($id) {
        $dartsStats = $this->getSingleStat($id, 'DartsStats');
        $dartsStats[request('x')][request('y')][0] += 1;
        return $this->startConditions()
            ->where('id', $id)
            ->update(['DartsStats' => $dartsStats]);
    }
    public function updStudentsStats($id) {
        $studentsStats = $this->getSingleStat($id, 'StudentsStats');
        $score = request('score');
        $index = $score > 0 ? 1 : 2;
        $studentsStats[request('name')][$index] += abs($score);
        return $this->setStudentsStats($id, $studentsStats);
    }
    public function updStudentStatus($id) {
        $studentsStats = $this->getSingleStat($id, 'StudentsStats');
        $name = request('name');
        foreach($studentsStats as $i => $stat) {
            if($stat[0][0] == $name) {
                $studentsStats[$i][0][3] = intval(request('status'));
                break;
            }
        }
        return $this->setStudentsStats($id, $studentsStats);
    }
    public function setStudentsStats($id, $data) {
        return $this->startConditions()
            ->where('id', $id)
            ->update(['StudentsStats' => $data]);
    }
    public function setLessonStatus($id) {
        $status = request('status');
        return $this->startConditions()
            ->where('id', $id)
            ->update(['status' => $status]);
    }
    public function getSingleStat($id, $stat) {
        return $this->startConditions()
            ->select($stat)
            ->where('id', $id)
            ->first()[$stat];
    }
    public function select($id, $columns) {
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
