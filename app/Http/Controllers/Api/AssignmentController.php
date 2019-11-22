<?php

namespace App\Http\Controllers\Api;

use App\Classroom;
use App\Http\Controllers\Controller;
use App\Repositories\AssignmentRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class AssignmentController extends Controller
{
    protected $repository;

    public function __construct() {
        $classroom = Classroom::getBySlug(Route::current()->slug);
        $this->repository = new AssignmentRepository($classroom, Route::current()->teachableId);
    }

    public function create(Request $request)
    {
        return apiResponse(200, $this->repository->create($request));
    }

    public function viewAssignment()
    {
        return apiResponse(200, $this->repository->view());
    }

    public function listSubmission()
    {
        return apiResponse(200, $this->repository->listSubmission());
    }

    public function list(Request $request)
    {
        return apiResponse(200, $this->repository->list($request));
    }

    public function uploadSubmission(Request $request)
    {
        return apiResponse(200, $this->repository->uploadSubmission($request), 'Your submission has been successfully uploaded');
    }
}
