<?php

namespace App\Http\Controllers;

use App\Repositories\SchoolRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->middleware('auth');
        $this->repos = app(SchoolRepository::class);
    }
    public function index() {
        if(Auth::user()->school_id == null) {
            return view('admin.schools');
        }
        return view('admin.singleSchool');
    }
    public function all()
    {
        return $this->repos->getList();
    }
    public function get($id) {
        return $this->repos->get($id);
    }
    public function add() {
        return $this->repos->add();
    }
}
