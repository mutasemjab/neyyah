<?php

namespace App\Http\Controllers\Api;

use App\Models\City;
use Illuminate\Http\JsonResponse;

class CityController extends ApiController
{
    /**
     * Get all cities ordered by id.
     */
    public function index(): JsonResponse
    {
        $cities = City::orderBy('id')->get();

        return $this->success($cities->map(fn ($c) => [
            'id'      => $c->id,
            'name_ar' => $c->name_ar,
            'name_en' => $c->name_en,
        ]));
    }
}
