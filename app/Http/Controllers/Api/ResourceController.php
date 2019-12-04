<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ResourceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class ResourceController extends Controller
{

    protected $repository;

    public function __construct() {
        $classroom = \App\Classroom::getBySlug(Route::current()->slug);
        $this->repository = new ResourceRepository($classroom, Route::current()->teachableId);
    }

    public function create(Request $request)
    {
        return apiResponse(200,$this->repository->create($request));
    }

    public function list(Request $request)
    {
        return apiResponse(200, $this->repository->list($request));
    }

    public function details()
    {
        return apiResponse(200, $this->repository->details());
    }

    public function delete()
    {
        return apiResponse(200, $this->repository->delete(),'Resource is deleted');
    }

    public function trashed(Request $request)
    {
        return apiResponse(200, $this->repository->trashed($request));
    }

    public function hardDelete()
    {
        return apiResponse(200, $this->repository->hardDelete(), 'Resource is really gone');
    }

}
