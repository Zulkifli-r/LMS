<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    protected $repository;

    public function __construct() {
        $this->repository = new \App\Repositories\ClassesRepository();
    }

    public function index()
    {
        return apiResponse(200, $this->repository->index());
    }

    public function detailCreated()
    {
        return apiResponse(200, $this->repository->created());
    }

    public function detailJoined()
    {
        return apiResponse(200, $this->repository->joined());
    }
}
