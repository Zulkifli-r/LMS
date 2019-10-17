<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\ValidationException;
use App\Http\Resources\Classroom as ClassroomResource;
use App\Repositories\Interfaces\ClassroomInterface;
use App\Repositories\Traits\RequestParameter;
use App\User;
use Illuminate\Support\Facades\Validator;

class ClassroomRepository implements ClassroomInterface
{
    use RequestParameter;

    protected $classroom;

    public function __construct( Classroom $classroom) {
        $this->classroom = $classroom;
    }

    // get all classroom
    public function all()
    {

    }

    public function with(array $relations)
    {

    }

    // Create new classroom
    public function create($data, User $user)
    {
        \DB::transaction(function() use($data, $user) {

            // create classroom
            $validData = $this->validator($data);

            if ($validData->fails()) {
                throw new ValidationException($validData->errors());
            }

            $this->classroom->fill($validData->validated());
            $user->classroom()->save($this->classroom);

            // create classroomuser
            $user->classrooms()->save($this->classroom);

            // assign classroomuser role as a teacher
            $classroomUser = $this->classroom->classroomUsers()->first();
            $classroomUser->assignRole(['teacher']);
        });

        return new ClassroomResource($this->classroom);
    }

    public function update()
    {

    }

    private function validator(array $data){
        return Validator::make($data, [
            'name' => 'string|required|max:255',
        ]);
    }

    public function myClassroom(User $user)
    {
        return ClassroomResource::collection($user->classrooms);
    }
}
