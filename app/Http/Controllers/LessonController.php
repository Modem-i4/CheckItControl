<?php

namespace App\Http\Controllers;


use App\Repositories\LessonRepository;

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
        return $this->repos->add();
    }
    public function remove() {
        $this->middleware('auth');
        $id = request('id');
        return $this->repos->remove($id);
    }
    public function setQuizStats($id) {
        $data = request('data');
        $data = json_decode($data)->data;
        return $this->repos->setQuizStats($id, $data);
    }
    public function getQuizStats($id) {
        return $this->repos->getQuizStats($id);
    }
    public function setDartsStats($id) {
        $data = request('data');
        $data = json_decode($data)->data;
        return $this->repos->setDartsStats($id, $data);
    }
    public function setStudentsStats($id) {
        $data = request('data')[0];
        return $this->repos->setStudentsStats($id, $data);
    }
    public function testStats($id) {
        return $this->repos->testStats($id);
    }
}
