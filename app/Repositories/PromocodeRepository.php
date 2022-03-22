<?php


namespace App\Repositories;


use App\Models\Promocode;
use Illuminate\Support\Facades\Auth;

class PromocodeRepository
{
    function startConditions()
    {
        return app(Promocode::class);
    }
    public function checkCode($code)
    {
        return $this->startConditions()
            ->where('code', $code)
            ->value('activated');
    }
    public function removePromocode($code) {
        return $this->startConditions()
            ->where('code', $code)
            ->update(['activated' => true]);
    }
}
