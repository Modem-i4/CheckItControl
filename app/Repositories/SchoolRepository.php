<?php


namespace App\Repositories;


use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SchoolRepository
{
    function startConditions()
    {
        return app(School::class);
    }
    public function getList()
    {
        $perPage = request('perPage');
        $columns = [
            'base.id',
            'base.title',
            DB::raw("CONCAT('[\"',
                            GROUP_CONCAT(users.name
                            SEPARATOR '\",\"'), '\"]')
                            as teachers")
        ];
        return $this->startConditions()
            ->from('schools as base')
            ->select($columns)
            ->leftJoin('users', 'base.id', '=', 'users.school_id')
            ->filter()
            ->groupBy('base.id')
            ->paginate($perPage);
    }
    public function get($id) {
        $columns = [
            'title',
        ];
        return $this->startConditions()
            ->where('id', $id)
            ->select($columns)
            ->first();
    }
    public function add() {
        $title = request('data.title');
        $userId = Auth::id();
        return $this->startConditions()
            ->insertGetId([
                'title' => $title,
                'creator_id' => $userId
            ]);
    }
}
