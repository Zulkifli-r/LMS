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
        return apiResponse(200, $this->repository->createQuestion($request, Route::current()->quiz));
    }

    public function updateQuestion(Request $request)
    {
        return apiResponse(200, $this->repository->updateQuestion($request, Route::current()->quiz, Route::current()->question ));
    }

    public function deleteQuestion()
    {
        return apiResponse(200, $this->repository->deleteQuestion(Route::current()->quiz, Route::current()->question ));
    }

    public function update(Request $request)
    {
        return apiResponse(200, $this->repository->update($request, Route::current()->quiz));
    }

    public function publish()
    {
        return apiResponse(200, $this->repository->publish(Route::current()->quiz));
    }

    public function unpublish()
    {
        return apiResponse(200, $this->repository->unpublish(Route::current()->quiz));
    }

    public function list(Request $request)
    {
        return apiResponse(200, $this->repository->list($request));
    }

    public function details(Request $request)
    {
        return apiResponse(200, $this->repository->details($request, Route::current()->quiz));
    }
}
