<?php

namespace App\Http\Controllers;

use App\Repositories\QuizRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    private $repos;
    public function __construct()
    {

        $this->repos = app(QuizRepository::class);
    }
    public function index() {
        $this->middleware('auth');
        return view('admin.quizzes');
    }
    public function quizIndex($id) {
        $this->middleware('auth');
        return view('admin.singleQuiz');
    }
    public function all()
    {
        $this->middleware('auth');
        return $this->repos->getList();
    }
    public function get($id) {
        $this->middleware('auth');
        $response = $this->repos->getSingle($id);
        $author = Auth::id();
        $response->editable = $author == $response->author;
        return $response;
    }
    public function update($id) {
        $this->middleware('auth');
        return $this->repos->update($id);
    }
    public function getList($id) {
        $this->middleware('auth');
        return $this->repos->getListBySubject($id);
    }
    public function getQuizPureJSON($id) {
        return $this->repos->getQuizJSON($id);
    }
    public function getQuizFormattedJSON($id): string
    {
        $JSON = $this->repos->getQuizJSON($id);
        return $this->formatJSON($JSON);
    }
    public function getDartsPureJSON($id) {
        return $this->repos->getDartsJSON($id);
    }
    public function getDartsFormattedJSON($id): string
    {
        $JSON = $this->repos->getDartsJSON($id);
        return $this->formatJSON($JSON);
    }
    public function formatJSON($JSON): string
    {
        $count = count($JSON);
        $JSON =  json_encode($JSON);
        return '{"c2array":true,"size":['.$count.',4,1],"data":'.$JSON.'}';
    }
    public function add() {
        return $this->repos->add();
    }
    public function favorite($id) {
        return $this->repos->favorite($id);
    }
}
