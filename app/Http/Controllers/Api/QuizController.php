<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\QuizRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class QuizController extends Controller
{
    protected $repository;

    public function __construct() {
        $classroom = \App\Classroom::getBySlug(Route::current()->slug);
        $this->repository = new QuizRepository($classroom, Route::current()->teachableId);
    }

    public function create(Request $request)
    {
        return apiResponse(200, $this->repository->save($request));
    }

    public function createQuestion(Request $request)
    {
        return apiResponse(200, $this->repository->createQuestion($request));
    }

    public function updateQuestion(Request $request)
    {
        return apiResponse(200, $this->repository->updateQuestion($request, Route::current()->question ));
    }

    public function deleteQuestion()
    {
        return apiResponse(200, $this->repository->deleteQuestion(Route::current()->question ));
    }

    public function update(Request $request)
    {
        return apiResponse(200, $this->repository->update($request));
    }

    public function publish()
    {
        return apiResponse(200, $this->repository->publish());
    }

    public function unpublish()
    {
        return apiResponse(200, $this->repository->unpublish());
    }

    public function list(Request $request)
    {
        return apiResponse(200, $this->repository->list($request));
    }

    public function details(Request $request)
    {
        return apiResponse(200, $this->repository->details($request));
    }

    public function delete()
    {
        return apiResponse(200, $this->repository->delete(), 'Quiz deleted');
    }

    public function trashed(Request $request)
    {
        return apiResponse(200, $this->repository->trashed($request));
    }

    public function hardDelete()
    {
        return apiResponse(200, $this->repository->hardDelete(), 'Quiz hard deleted');
    }

    public function attempt()
    {
        return apiResponse(200, $this->repository->attempt());
    }

    public function updateAttempt(Request $request)
    {
        return apiResponse(200, $this->repository->updateAttempt($request));
    }
}
