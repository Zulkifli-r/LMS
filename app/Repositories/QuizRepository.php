<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\ValidationException;
use App\Http\Resources\Quiz;
use App\Http\Resources\QuizCollection;
use Illuminate\Support\Facades\Validator;

class QuizRepository
{
    private const TEACHABLE_TYPE = 'quizz';

    protected $classroom;
    protected $quiz;
    protected $teachable;

    public function __construct(Classroom $classroom, $teachable = null) {
        $this->classroom = $classroom;
        $this->teachable = $teachable?Teachable::find($teachable):null;
        $this->quiz = new \App\Quiz();
    }

    public function save($data)
    {
        $validData = $this->validateQuizData($data->all());

        if ($validData->fails()) {
            throw new ValidationException($validData->errors());
        }

        \DB::transaction(function() use($data) {
            // create/update quizz
            $this->quiz->fill($data->only($this->quiz->getFillable()));

            $this->quiz->grading_method = 'standard';
            $this->quiz->save();
            // create teachable
            $teachable = new \App\Teachable();
            $teachable = $teachable->fill($data->only($teachable->getFillable()));
            $teachable->teachable_type = self::TEACHABLE_TYPE;
            $teachable->classroom_id = $this->classroom->id;

            $this->quiz->teachable()->save($teachable);
            // create teachable user
            $classroomStudents = $this->classroom->students()->get();
            foreach ($classroomStudents as $key => $classroomStudent) {
                $teachable_user = New \App\TeachableUser();
                $teachable_user->classroom_user_id = $classroomStudent->id;

                $teachable->teachableUsers()->save($teachable_user);
            }
        });
        return new Quiz($this->quiz);
    }

    public function update($data, $quiz)
    {
        $this->quiz = \App\Quiz::getById($quiz);

        $validData = $this->validateQuizData($data->all());

        if ($validData->fails()) {
            throw new ValidationException($validData->errors());
        }

        \DB::transaction(function() use($data) {
            // update quizz
            $this->quiz->fill($data->only($this->quiz->getFillable()));
            $this->quiz->save();
            // update teachable
            $teachable = $this->quiz->teachable;
            $teachable = $teachable->fill($data->only($teachable->getFillable()));
            $this->quiz->teachable()->save($teachable);
        });
        return new Quiz($this->quiz);
    }

    public function publish($quiz)
    {
        $this->quiz = \App\Quiz::getById($quiz);
        $this->quiz->teachable->available_at = \Carbon\Carbon::now();
        $this->quiz->teachable->save();

        return true;
    }

    public function unpublish($quiz)
    {
        $this->quiz = \App\Quiz::getById($quiz);
        $this->quiz->teachable->available_at = null;
        $this->quiz->teachable->save();

        return true;
    }

    public function createQuestion($data, $quiz)
    {
        $question = new QuestionRepository($this->classroom, $quiz);
        return $question->create($data);
    }

    public function updateQuestion($data, $quiz, $question)
    {
        $question = new QuestionRepository($this->classroom, $quiz, $question);
        return $question->update($data);
    }

    public function deleteQuestion($quiz, $question)
    {
        $question = new QuestionRepository($this->classroom, $quiz, $question);
        return $question->delete();
    }

    public function forceDeleteQuestion($quiz, $question)
    {
        $question = new QuestionRepository($this->classroom, $quiz, $question);
        return $question->forceDelete();
    }

    private function validateQuizData($data){
        return Validator::make($data, [
            'title' => 'string|required|max:255',
            'description' => 'string|required',
            'time_limit' => 'required|min:0',
            // 'expires_at'     => 'date',
        ]);
    }

    public function list($request)
    {
        $perPage = $request->has('perPage')?$request->perPage:$this->quiz->getPerPage();
        $data = $this->classroom->quizzes()->paginate($perPage);

        return QuizCollection::make($data);
    }

    public function details($request, $quiz)
    {

        $this->quiz = \App\Quiz::getById($quiz);

        $res = new Quiz($this->quiz);

        $includes = [];

        if ($request->has('with_questions') && $request->with_questions) {
            $includes['questions'] = true;
        }

        return $res->includes($includes);
    }

    public function delete($quiz)
    {
        $this->quiz = \App\Quiz::getById($quiz);

        if ($this->quiz->delete()) {
            return true;
        }
    }

    public function trashed($request)
    {
        $quiz = $this->classroom->quizzes()->onlyTrashed()->paginate($request->perPage ?? $this->quiz->getPerPage());

        return new QuizCollection($quiz);
    }

    public function hardDelete($quiz)
    {
        // force delete resource and clear media collection
        $this->quiz = \App\Quiz::getById($quiz);
        $this->quiz->teachable->teachableUsers()->delete();
        $this->quiz->teachable->forceDelete();
        $this->quiz->clearMediaCollection();
        $this->quiz->forceDelete();

        return true;
    }

    public function attempt($quiz)
    {
        $this->quiz = \App\Quiz::getById($quiz);

        $attempt = new QuizAttemptRepository($this->classroom, $this->quiz);
        return $attempt->start();
    }
}
