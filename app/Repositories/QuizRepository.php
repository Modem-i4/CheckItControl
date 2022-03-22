<?php


namespace App\Repositories;


use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuizRepository
{
    function startConditions()
    {
        return app(Quiz::class);
    }
    public function getList()
    {
        $perPage = request('perPage');
        $kind = json_decode(request('repositoryFilters'))->kind ?? "";
        $columns = [
            'base.id',
            'base.title',
            'base.description',
            'base.author',
            DB::raw('COUNT(lessons.id) as total_lessons'),
            DB::raw('COUNT(favorites.user_id) > 0 as favorite')
        ];
        $action = $this->startConditions()
            ->from('quizzes as base')
            ->select($columns)
            ->leftJoin('lessons', 'base.id', '=', 'lessons.quiz_id')
            ->leftJoin('favorites', function($join){
                $join->on('favorites.quiz_id', '=', 'base.id');
                $join->where('favorites.user_id', '=', Auth::id());
            });
        switch ($kind) {
            case "authors":
                $action
                    ->where('base.author', NULL);
                break;
            case "users":
                $action
                    ->where('base.author', '<>', NULL)
                    ->where('verified', true);
                break;
            case "my_school":
                $action
                    ->leftJoin('users', 'base.author', '=', 'users.id')
                    ->leftJoin('schools', 'users.school_id', '=', 'schools.id')
                    ->where('schools.id', Auth::user()->school_id)
                    ->where('schools.id', '<>', NULL);
                break;
            case "own":
                $action
                    ->where('base.author', Auth::id());
                break;
            case "favorite":
                $action
                    ->having('favorite', true);
                break;
            default:
                $action
                    ->where('verified', true);
                break;
        }
        return $action
            ->filter()
            ->groupBy('base.id')
            ->paginate($perPage);
    }

    public function getSingle($id) {
        $columns = [
            'id',
            'title',
            'author',
            'description',
            'quizJSON',
            'dartsJSON',
            DB::raw('COUNT(favorites.user_id) > 0 as favorite')
        ];
        return $this->startConditions()
            ->select($columns)
            ->leftJoin('favorites', function($join){
                $join->on('favorites.quiz_id', '=', 'quizzes.id');
                $join->where('favorites.user_id', '=', Auth::id());
            })
            ->where('id', $id)
            ->get()[0];
    }

    public function update($id) {
        $author = Auth::id();
        return $this->startConditions()
            ->where('id', $id)
            ->where('author', $author)
            ->update([
                'title' => request('title'),
                'description' => request('description'),
                'quizJSON' => request('questions'),
                'dartsJSON' => request('darts'),
                ]);
    }

    public function getListBySubject($subject_id) {
        $user_id = Auth::id();
        $columns = [
            'id',
            'title',
        ];
        return $this->startConditions()
            ->select($columns)
            ->leftJoin('favorites', 'favorites.quiz_id', '=', 'quizzes.id')
            ->where('favorites.user_id', $user_id)
            ->where('subject_id', $subject_id)
            ->get();
    }
    public function getQuizJSON($id) {
        $columns = [
            'quizJSON',
        ];
        return $this->startConditions()
            ->select($columns)
            ->where('id', $id)
            ->first()['quizJSON'];
    }
    public function getDartsJSON($id) {
        $columns = [
            'dartsJSON',
        ];
        return $this->startConditions()
            ->select($columns)
            ->where('id', $id)
            ->first()['dartsJSON'];
    }
    public function add() {
        $title = request('data.title');
        $description =  request('data.description');
        $subject_id =  request('data.subject_id');
        $author = Auth::id();
        return $this->startConditions()
            ->insertGetId([
                'title' => $title,
                'description' => $description,
                'subject_id' => $subject_id,
                'author' => $author,
            ]);
    }
    public function favorite($quiz_id) {
        $fav = request('favorite');
        $user_id = Auth::id();
        if($fav) {
            return $this->startConditions()
                ->insertGetId([
                    'user_id' => $user_id,
                    'quiz_id' => $quiz_id
                ]);
        }
        else {
            return app(Quiz::class)
                ->delete([
                    'user_id' => $user_id,
                    'quiz_id' => $quiz_id
                ]);
        }
    }
}
