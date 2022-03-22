<?php

namespace App\Http\Controllers;

use App\Repositories\SubjectRepository;
use Illuminate\Http\Request;

class SubjectController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->middleware('auth');

        $this->repos = app(SubjectRepository::class);
    }
    public function all()
    {
        return $this->repos->getList();
    }
}
