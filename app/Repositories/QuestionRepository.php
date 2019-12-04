<?php

namespace App\Repositories;

use App\Exceptions\ValidationException;
use App\Http\Resources\Question;
use Illuminate\Support\Facades\Validator;

class QuestionRepository
{
    protected $classroom;
    protected $quiz;
    protected $question;

    public function __construct($classroom, $quiz, $question = null) {
        $this->classroom = $classroom;
        $this->quiz = $quiz;
        $this->question = $question ? \App\Question::getById($question) :new \App\Question();
    }

    public function create($data)
    {
        $validatedData = $this->validateQuestion($data->all());
        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        \DB::transaction(function() use ($data){
            // create question
            $this->question->fill($data->only($this->question->getFillable()));

            $this->question->save();

            // create question_quiz
            $this->quiz->questions()->attach($this->question->id,['weight' => $data->weight]);

            // create choice item
            if ($data->has('choices')) {
                $this->question->choiceItems()->saveMany($this->choices($data->choices));
            }

        });

        return new Question($this->question);

    }

    public function update($data)
    {
        $validatedData = $this->validateQuestion($data->all());
        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        \DB::transaction(function() use ($data){
            // fill the question
            $this->question->fill($data->only($this->question->getFillable()));

            // update the question
            $this->question->save();

            $this->quiz->questions()->updateExistingPivot($this->question->id,['weight' => $data->weight]);
            // update/create choice item
            if ($data->has('choices')) {
                $this->question->choiceItems()->delete();
                $this->question->choiceItems()->saveMany($this->choices($data->choices));
            }

        });

        return new Question($this->question);
    }

    public function delete()
    {
        \DB::transaction(function(){
            $this->quiz->questions()->detach($this->question->id);
            $this->question->delete();
        });
        return true;
    }

    public function forceDelete()
    {
        \DB::transaction(function(){
            $this->question->choiceItems()->delete();
            $this->question->forceDelete();
        });
        return true;
    }

    private function validateQuestion($data){
        return Validator::make( $data , [
            'question_type' => 'required|string',
            'weight' => 'required|min:0',
            'content' => 'required|string',
            'answer' => 'nullable|required_if:question_type, fill-in|array|min:1',
            'choices' => 'required_if:question_type,==,multiple-choice|required_if:question_type,==,boolean|required_if:question_type,==,multiple-response|' . ( $data == 'multiple-response' ? 'array|min:1' : 'array:max:1' ),
            'choices.*.choice_text' => 'required|string',
            'choices.*.is_correct' => 'required|boolean',
        ]);
    }

    private function choices($choices){
        foreach ($choices as $key => $value) {
            $data[$key] = new \App\QuestionChoiceItem(['choice_text' => $value['choice_text'], 'is_correct' => $value['is_correct']]);
        }

        return $data;
    }

}
