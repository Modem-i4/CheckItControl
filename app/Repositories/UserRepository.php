<?php


namespace App\Repositories;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    function startConditions()
    {
        return app(User::class);
    }
    public function getList()
    {
        $perPage = request('perPage');
        $columns = [
            'id',
            'name',
            'email',
            'role',
            'created_at as reg_time',
            'notifications_receiver',
        ];

        return $this->startConditions()
            ->select($columns)
            ->filter()
            ->paginate($perPage);
    }
    public function getBySchool($school_id) {
        $columns = [
            'name',
        ];
        return $this->startConditions()
            ->select($columns)
            ->where('school_id', $school_id)
            ->pluck('name');
    }
    public function setSchool($school_id) {
        $userId = Auth::id();
        return $this->startConditions()
            ->where('id', $userId)
            ->update(['school_id' => $school_id]);
    }
    public function activateAccount() {
        $userId = Auth::id();
        return $this->startConditions()
            ->where('id', $userId)
            ->update(['role' => 'premium']);
    }
}
