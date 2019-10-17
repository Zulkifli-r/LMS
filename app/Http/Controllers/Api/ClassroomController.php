<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ClassroomRepository;
use App\User;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    protected $repository;

    public function __construct(ClassroomRepository $repository) {
        $this->repository = $repository;
    }

    public function create(Request $request)
    {
        $response = $this->repository->create($request->all(), auth()->user());

        return apiResponse(200,$response);

    }

    public function myClassroom()
    {
        return apiResponse(200,$this->repository->myClassroom(auth()->user())) ;
    }
}
