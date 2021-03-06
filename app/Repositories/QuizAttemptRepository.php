<?php

namespace App\Repositories;

use App\Exceptions\UnauthorizeException;
use App\Exceptions\ValidationException;
use App\Http\Resources\QuizAttempt;
use Illuminate\Support\Facades\Validator;

class QuizAttemptRepository
{
    protected $classroom;
    protected $quiz;
    protected $teachableUser;
    protected $teachable;
    protected $user;
    protected $quizAttempt;

    public function __construct($classroom, $quiz) {
        $this->classroom = $classroom;
        $this->quiz = $quiz;
        $this->user = auth('api')->user();
        $this->teachable = $quiz->teachable;
    }

    // start attempt
    public function start()
    {
        if ( ! $this->classroom->isStudent()) {
            throw new UnauthorizeException('not this classrooms student');
        }

        $this->teachableUser = $this->user->teachableUser($this->classroom,$this->quiz)->first();
        if (!$this->teachableUser) {
            throw new UnauthorizeException('this quiz is not for you');
        }

        $this->filterActiveQuiz();

        $previousAttempts = $this->teachableUser->quizAttempts;

        $unfinishedAttempt = $previousAttempts ? $previousAttempts->where( 'completed_at', null )->first():null;

        $this->quizAttempt = $unfinishedAttempt ?? new \App\QuizAttempt();


        if ( !$unfinishedAttempt ) {
            $this->teachableUser->teachable->quiz->randomizeQuestions();
            $snapshot = \App\Quiz::prepareSnapshot($this->teachableUser->teachable->quiz->questions);

            $this->quizAttempt->teachable_user_id = $this->teachableUser->id;

            $this->quizAttempt->questions = $snapshot['questions']->toJson();
            $this->quizAttempt->answers = $snapshot['answers']->toJson();
        }

        $this->quizAttempt->attempt = $unfinishedAttempt ? $unfinishedAttempt->attempt : $previousAttempts->max('attempt') + 1;
        // ($previousAttempts ? $previousAttempts->max( 'attempt' ):0) + 1 : $unfinishedAttempt->attempt;

        \DB::transaction(function(){
            $this->quizAttempt->save();
        });

        return new QuizAttempt($this->quizAttempt,[
            'answers' => true,
            'questions' => true
        ]);

    }

    public function update($request)
    {
        // dd($request->all());
        $validatedData = $this->validateUpdate($request->all());

        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        if ( ! $this->classroom->isStudent()) {
            throw new UnauthorizeException('not this classrooms student');
        }

        $this->teachableUser = $this->user->teachableUser($this->classroom,$this->quiz)->first();
        if (!$this->teachableUser) {
            throw new UnauthorizeException('this quiz is not for you');
        }

        $this->quizAttempt = \App\QuizAttempt::getByTeachableUserId($this->teachableUser->id);

        // dd($request->answer);

        switch ($request->context) {
            case 'answer':
                $this->filterActiveQuiz();
                $this->quizAttempt->answer( collect($request->answer) , $this->quizAttempt->grading_method );
                break;
            case 'scoring':
                $this->quizAttempt->scoring( collect($request->scores) );
                break;
            case 'complete':
                $this->quizAttempt->complete();
                $this->quizAttempt->teachableUser->complete();
                return response()->json('success');
        }

        return new QuizAttempt($this->quizAttempt,[
            'answers' => true,
            'questions' => true,
            'hideScore' => true
        ]);

    }
    // submit quiz attempt
    public function submit()
    {
        $this->filterActiveQuiz();

        $this->teachableUser = $this->user->teachableUser($this->classroom,$this->quiz)->first();
        $this->quizAttempt = \App\QuizAttempt::getByTeachableUserId($this->teachableUser->id);
        $this->quizAttempt->complete();
        $this->teachableUser->complete();
        return true;
        // dd($this->teachableUser,$this->quizAttempt);
    }
    // review quiz
    // quiz result

    private function validateUpdate($data){
        return Validator::make($data, [
            'context' => 'required|in:answer,complete,scoring',
            'answer' => 'required_if:context,==,answer',
            'answer.questionId' => 'required_if:context,==,answer',
        ]);
    }

    private function filterActiveQuiz(){
        if ( $this->teachable->available_at == null || $this->teachable->expires_at < \Carbon\Carbon::now()) {
            throw new UnauthorizeException('quiz not available');
        }
    }

}
