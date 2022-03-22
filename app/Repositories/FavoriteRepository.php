<?php


namespace App\Repositories;


use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FavoriteRepository
{
    function startConditions()
    {
        return app(Favorite::class);
    }
    public function favorite($quiz_id) {
        $fav = request('favorite');
        $user_id = Auth::id();
        if($fav) {
            return $this->startConditions()
                ->insertGetId([
                    'user_id' => $user_id,
                    'quiz_id' => $quiz_id
                ]);
        }
        else {
            return $this->startConditions()
                ->where([
                    'user_id' => $user_id,
                    'quiz_id' => $quiz_id
                ])
                ->delete();
        }
    }
}
