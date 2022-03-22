<?php


namespace App\Repositories;


use App\Models\Group;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GroupRepository
{
    function startConditions()
    {
        return app(Group::class);
    }
    public function getList()
    {
        $perPage = request('perPage');
        $columns = [
            'id',
            'title',
            'studentsJSON as students'
        ];
        $school_id = Auth::user()->school_id;
        return $this->startConditions()
            ->select($columns)
            ->where('school_id', $school_id)
            ->filter()
            ->paginate($perPage);
    }

    public function add() {
        $title = request('data.title');
        $userId = Auth::id();
        $school_id = Auth::user()->school_id;
        return $this->startConditions()
            ->insertGetId([
                'title' => $title,
                'school_id' => $school_id,
                'creator_id' => $userId
            ]);
    }

    public function setStudents($id) {
        $students = request('students');
        return $this->startConditions()
            ->where('id',$id)
            ->update(['studentsJSON' => $students]);
    }

    public function getMinList() {
        $school_id = Auth::user()->school_id;
        $columns = [
            'id',
            'title',
        ];
        return $this->startConditions()
            ->select($columns)
            ->where('school_id', $school_id)
            ->where('studentsJSON', "LIKE", '%","%')
            ->get();
    }
    public function getBySchool($school_id) {
        $columns = [
            'title',
        ];
        return $this->startConditions()
            ->select($columns)
            ->where('school_id', $school_id)
            ->pluck('title');
    }
}
