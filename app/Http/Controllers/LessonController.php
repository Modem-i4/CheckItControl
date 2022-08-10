<?php

namespace App\Http\Controllers;


use App\Repositories\LessonRepository;
use App\Repositories\QuizRepository;

class LessonController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->repos = app(LessonRepository::class);
    }
    public function index() {
        $this->middleware('auth');
        return view('admin.lessons');
    }
    public function all()
    {
        $this->middleware('auth');
        return $this->repos->getList();
    }
    public function single($id) {
        $this->middleware('auth');
        return view('admin.singleLesson', ['id' => $id]);
    }
    public function get($id) {
        $this->middleware('auth');
        return $this->repos->get($id);
    }
    public function add() {
        $this->middleware('auth');
        $quizId = request('data.quiz_id');
        $quizRepos = app(QuizRepository::class);
        $quizCount = [
            'posts' => count($quizRepos->getQuizJSON($quizId)),
            'darts' => count($quizRepos->getDartsJSON($quizId)),
        ];
        return $this->repos->add($quizId, $quizCount);
    }
    public function remove() {
        $this->middleware('auth');
        $id = request('id');
        return $this->repos->remove($id);
    }
    public function updStats($id) {
        $result = null;
        $type = request('type');
        if($type === "posts") {
            $result = $this->repos->updPostsStats($id);
        }
        elseif($type === "darts") {
            $result = $this->repos->updDartsStats($id);
        }
        if(request('name') !== "" && $result) {
            $result = $this->repos->updStudentsStats($id);
        }
        return $result;
    }
    public function updStudentStatus($id) {
        return $this->repos->updStudentStatus($id);
    }
    public function getLessonStatus($id) {
        $columns = [
            'status',
        ];
        return $this->repos->select($id, $columns);
    }
    public function getStudentStats($id) {
        $columns = [
            'status',
            'StudentsStats'
        ];
        return $this->repos->select($id, $columns);
    }
    public function getQuizStats($id) {
        $columns = [
            'status',
            'StudentsStats',
            'QuizStats',
            'DartsStats'
        ];
        return $this->repos->select($id, $columns);
    }
    public function setStudentsStats($id) {
        $data = request('data');
        return $this->repos->setStudentsStats($id, $data);
    }
    public function setLessonStatus($id) {
        return $this->repos->setLessonStatus($id);
    }
    public function testStats($id) {
        return $this->repos->testStats($id);
    }
}
