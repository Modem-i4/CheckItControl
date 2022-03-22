<?php

namespace App\Http\Controllers;

use App\Repositories\FavoriteRepository;

class FavoriteController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->repos = app(FavoriteRepository::class);
    }
    public function favorite($id) {
        return $this->repos->favorite($id);
    }
}
