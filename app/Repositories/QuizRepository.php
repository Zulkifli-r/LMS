<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Http\Resources\Quiz;
use App\Http\Resources\QuizCollection;
use App\Teachable;
use Illuminate\Support\Facades\Validator;

class QuizRepository
{
    private const TEACHABLE_TYPE = 'quizz';

    protected $classroom;
    protected $quiz;
    protected $teachable;

    public function __construct(Classroom $classroom, $teachable = null) {
        $this->classroom = $classroom;
        $this->teachable = $teachable?Teachable::findOrNotFound($teachable):null;

        if ($this->teachable && $this->teachable->teachable_type != 'quizz') {
            throw new NotFoundException('Quiz');
        }

        $this->quiz = new \App\Quiz();
    }

    public function save($data)
    {
        $validData = $this->validateQuizData($data->all());

        if ($validData->fails()) {
            throw new ValidationException($validData->errors());
        }

        $teachable = new \App\Teachable();

        \DB::transaction(function() use($data, $teachable) {
            // create/update quizz
            $this->quiz->fill($data->only($this->quiz->getFillable()));

            $this->quiz->grading_method = 'standard';
            $this->quiz->save();
            // create teachable

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
        return new Quiz($teachable);
    }

    public function update($data)
    {
        $this->quiz = $this->teachable->quiz;

        $validData = $this->validateQuizData($data->all());

        if ($validData->fails()) {
            throw new ValidationException($validData->errors());
        }

        \DB::transaction(function() use($data) {
            // update quizz
            $this->quiz->fill($data->only($this->quiz->getFillable()));
            $this->quiz->save();
            // update teachable
            $teachable = $this->teachable;
            $teachable = $teachable->fill($data->only($teachable->getFillable()));
            $this->quiz->teachable()->save($teachable);
        });
        return new Quiz($this->teachable);
    }

    public function publish()
    {
        $this->teachable->available_at = \Carbon\Carbon::now();
        $this->teachable->save();

        return true;
    }

    public function unpublish()
    {
        $this->teachable->available_at = null;
        $this->teachable->save();

        return true;
    }

    public function createQuestion($data)
    {
        $question = new QuestionRepository($this->classroom, $this->teachable->quiz);
        return $question->create($data);
    }

    public function updateQuestion($data, $question)
    {
        $question = new QuestionRepository($this->classroom, $this->teachable->quiz, $question);
        return $question->update($data);
    }

    public function deleteQuestion($question)
    {
        $question = new QuestionRepository($this->classroom, $this->teachable->quiz, $question);
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
        $data = $this->classroom->quizzes()->latest()->paginate($perPage);

        return new QuizCollection($data);
    }

    public function details($request)
    {
        $res = new Quiz($this->teachable);

        $includes = [];

        if ($request->has('with_questions') && $request->with_questions) {
            $includes['questions'] = true;
        }

        return $res->includes($includes);
    }

    public function delete()
    {
        return $this->teachable->delete();
    }

    public function trashed($request)
    {
        $quiz = $this->classroom->quizzes()->onlyTrashed()->paginate($request->perPage ?? $this->quiz->getPerPage());

        return new QuizCollection($quiz);
    }

    public function hardDelete()
    {
        \DB::transaction(function(){
            // force delete resource and clear media collection
            $this->teachable->teachableUsers()->delete();
            $this->teachable->quiz->questions()->forceDelete();
            $this->quiz->clearMediaCollection();
            $this->teachable->quiz->forceDelete();
            $this->teachable->forceDelete();
        });

        return true;
    }

    public function attempt()
    {
        $attempt = new QuizAttemptRepository($this->classroom, $this->teachable->quiz);
        return $attempt->start();
    }

    public function updateAttempt($request)
    {
        $attempt = new QuizAttemptRepository($this->classroom, $this->teachable->quiz);
        return $attempt->update($request);
    }


    public function submitAttempt($request)
    {
        $attempt = new QuizAttemptRepository($this->classroom, $this->teachable->quiz);
        return $attempt->submit();
    }

}
