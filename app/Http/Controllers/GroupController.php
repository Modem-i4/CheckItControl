<?php

namespace App\Http\Controllers;

use App\Repositories\GroupRepository;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->middleware('auth');
        $this->repos = app(GroupRepository::class);
    }
    public function index() {
        return view('admin.groups');
    }
    public function all()
    {
        return $this->repos->getList();
    }
    public function list() {
        return $this->repos->getMinList();
    }
    public function get($id) {
        return $this->repos->getListBySubject($id);
    }
    public function add() {
        return $this->repos->add();
    }
    public function setStudents($id) {
        return $this->repos->setStudents($id);
    }
    public function getBySchool($school_id) {
        return $this->repos->getBySchool($school_id);
    }
}
