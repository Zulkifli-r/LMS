<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ClassroomRepository;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    protected $repository;

    public function __construct(ClassroomRepository $repository) {
        $this->repository = $repository;
    }

    public function create(Request $request)
    {
        $response = $this->repository->create($request, auth()->user());

        return apiResponse(200,$response);

    }

    public function myClassroom()
    {
        return apiResponse(200,$this->repository->myClassroom(auth()->user())) ;
    }

    public function details($slug)
    {
        return apiResponse(200,$this->repository->details($slug));
    }

    public function update(Request $request, $slug)
    {
        return apiResponse(200, $this->repository->update($request, $slug));
    }

    public function delete($slug)
    {
        return apiResponse(200, $this->repository->delete($slug));
    }

    public function trashed(Request $request)
    {
        return apiResponse(200, $this->repository->trashed($request));
    }

    public function hardDelete($slug)
    {
        return apiResponse(200, $this->repository->hardDelete($slug));
    }

    public function listStudents(Request $request, $slug)
    {
        return apiResponse(200, $this->repository->listStudents($request, $slug));
    }

    public function removeStudent(Request $request, $slug)
    {
        return apiResponse(200, $this->repository->removeStudent($request, $slug));
    }

    public function userStatus($slug)
    {
        return apiResponse(200, $this->repository->userStatus($slug));
    }


}
