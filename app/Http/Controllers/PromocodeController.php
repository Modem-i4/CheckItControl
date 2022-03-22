<?php

namespace App\Http\Controllers;

use App\Repositories\PromocodeRepository;
use App\Repositories\UserRepository;

class PromocodeController extends Controller
{
    private $repos;
    public function __construct()
    {
        $this->middleware('auth');
        $this->repos = app(PromocodeRepository::class);
    }
    public function checkCode() {
        $code = request('code');
        $entry = $this->repos->checkCode($code);
        if($entry === null) {
            return response("Wrong code", 400);
        }
        else if($entry === 1) {
            return response("Code is already used", 400);
        }
        else {
            $userRepository = app(UserRepository::class);
            if($userRepository->activateAccount($code) && $this->repos->removePromocode($code)) {
                return response("Success");
            }
            else
            {
                return response("Database error", 500);
            }
        }
    }
}
