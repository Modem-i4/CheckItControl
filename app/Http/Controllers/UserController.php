<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->middleware('auth');

        $this->repos = app(UserRepository::class);
    }
    public function index() {
        return view('admin.users');
    }
    public function all()
    {
        return $this->repos->getAllWithPaginateAndFiltering();
    }
    public function getBySchool($school_id) {
        return $this->repos->getBySchool($school_id);
    }
    public function setSchool($school_id) {
        $school_id = $school_id === "null" ? null : $school_id;
        return $this->repos->setSchool($school_id);
    }
}
