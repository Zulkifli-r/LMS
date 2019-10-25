<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Classroom;
use App\Repositories\DiscoverRepository;
use Illuminate\Http\Request;

class DiscoverController extends Controller
{
    protected $repository;

    public function __construct(DiscoverRepository $discover) {
        $this->repository = $discover;
    }

    public function byClassName(Request $request)
    {
        return apiResponse(200,$this->repository->byClassName($request));
    }

    public function byTags(Request $request)
    {
        return apiResponse(200,$this->repository->byTags($request));
    }
}
