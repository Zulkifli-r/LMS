<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Http\Resources\Classes;
use App\Http\Resources\Classroom as ClassroomResource;
use App\Http\Resources\ClassroomCollection;
use App\Http\Resources\ClassroomUserCollection;
use App\Http\Resources\ClassroomUserStatus;
use App\Http\Resources\UserCollection;
use App\Repositories\Interfaces\ClassroomInterface;
use App\Repositories\Traits\RequestParameter;
use App\User;
use Illuminate\Support\Facades\Validator;

class ClassroomRepository implements ClassroomInterface
{
    use RequestParameter;

    protected $classroom;

    public function __construct(Classroom $classroom) {
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
            $validData = $this->validator($data->all());

            if ($validData->fails()) {
                throw new ValidationException($validData->errors());
            }

            $this->classroom->fill($data->all());
            $classroom = $user->classroom()->save($this->classroom);

            // upload classroom image
            if ($data->has('image')) {
                $classroom->addMedia($data->image)->toMediaCollection('image');
            }
            //attach tag to classroom
            if ( isset($data['tags']) ) {
                $classroom->attachTags($data['tags']);
            }
            // create classroomuser
            $user->classrooms()->save($this->classroom);
            // assign classroomuser role as a teacher
            $classroomUser = $this->classroom->classroomUsers()->first();
            $classroomUser->assignRole(['teacher']);


        });

        return new ClassroomResource($this->classroom);
    }

    public function update($request, $slug)
    {
        $this->classroom = $this->classroom->getBySlug($slug);

        $validData = $this->validator($request->all());
        if ($validData->fails()) {
            throw new ValidationException($validData->errors());
        }

        $this->classroom->fill($request->all());
        $this->classroom->save();

        // upload classroom image
        if ($request->has('image')) {
            $this->classroom->clearMediaCollection('image');
            $this->classroom->addMedia($request->image)->toMediaCollection('image');
        }

        //attach tag to classroom
        if ( isset($request['tags']) ) {
            $this->classroom->attachTags($request['tags']);
        }

        return new ClassroomResource($this->classroom);
    }

    public function delete($slug)
    {
        $this->classroom = $this->classroom->getBySlug($slug);
        $this->classroom->delete();

        return true;
    }

    public function trashed($request)
    {
        $data = $this->classroom->onlyTrashed()->paginate($request->perPage ?? $this->classroom->getPerPage() );
        return new ClassroomCollection($data);
    }

    public function hardDelete($slug)
    {
        $this->classroom = $this->classroom->where('slug', $slug)->withTrashed();
        if ( $this->classroom->first() == null )
            throw new  NotFoundException('Class');

        $this->classroom = $this->classroom->first();

        $this->classroom->assignments()->forceDelete();
        $this->classroom->quizzes()->forceDelete();
        $this->classroom->resources()->forceDelete();
        $this->classroom->classroomUsers()->forceDelete();
        $this->classroom->clearMediaCollection();
        $this->classroom->forceDelete();

        return true;
    }

    // validate incoming request
    private function validator(array $data){
        return Validator::make($data, [
            'name' => 'string|required|max:255',
            'title' => 'string|required|max:255',
            'class_type' => 'string|required|in:public,private',
            'tags' => 'array'
        ]);
    }

    // get classroom by logged in user
    public function myClassroom(User $user)
    {
        return new Classes(auth('api')->user());
        // ClassroomResource::collection($user->classrooms->sortByDesc('created_at'));
    }

    public static function getClassroomBySlug(string $slug){
        $classroom = app()->make('App\Classroom');

        if ( $classroom = $classroom->where('slug' , $slug)->first() )
            return $classroom->where('slug' , $slug)->first();

        throw new NotFoundException('classroom');
    }

    public function details($slug)
    {
        $classroom = self::getClassroomBySlug($slug);
        return new ClassroomResource($classroom, ['students' => true, 'teachers' => true]) ;
    }

    public function listStudents($request, $slug)
    {
        $this->classroom = $this->classroom->getBySlug($slug);

        return new ClassroomUserCollection($this->classroom->students()->paginate($request->perPage ?? $this->classroom->getPerPage()));
    }

    public function removeStudent($request, $slug)
    {
        $this->classroom = $this->classroom->getBySlug($slug);
        $student = $this->classroom->classroomUsers()->with('user')->get()->where('user.email', $request->email);
        if ($student->first() ==  null) {
            throw new NotFoundException('Student');
        }

        $student->first()->delete();
        return true;
    }

    public function userStatus($slug)
    {
        $this->classroom = $this->classroom->getBySlug($slug);
        return new ClassroomUserStatus($this->classroom);
    }
}
