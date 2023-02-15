<?php

namespace App\Http\Controllers;

use App\Models\CouponCode;

class CouponCodesController extends Controller
{
    public function show($code)
    {
        //优惠卷不存在
        if (! $record = CouponCode::query()->where('code', $code)->first()) {
            abort(404);
        }

        $record->checkAvailable(request()->user());

        return $record;
    }
}
