<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Http\Resources\Assignment as AssignmentResource;
use App\Http\Resources\AssignmentCollection;
use App\Http\Resources\Teachable as TeachableResource;
use App\Teachable;
use Illuminate\Support\Facades\Validator;

class AssignmentRepository {

    private const TEACHABLE_TYPE = 'assignment';
    // Assignmen model object
    protected $assignment;
    // Current classroom object
    protected $classroom;
    // Classroom logged in user
    // protected $classroomUser;
    // classroom teachable
    protected $teachable;

    public function __construct(Classroom $classroom, $teachable = null) {
        $this->classroom = $classroom;
        $this->teachable = $teachable?Teachable::findOrNotFound($teachable):null;
        $this->assignment = new \App\Assignment();
        // $this->classroomUser = $this->classroom->classroomUser();
    }

    // create new assignment
    public function create($data)
    {
        $validatedData = $this->validateAssignment($data->all());

        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        // get the current classroom
        $classroom = $this->classroom;

        \DB::transaction(function() use($data, $classroom) {
            // create assignment
            $this->assignment->title = $data->title;
            $this->assignment->description = $data->description;
            $this->assignment->save();

            // create teachable
            $teachable = new \App\Teachable();
            $teachable->fill($data->only($teachable->getFillable()));
            $teachable->teachable_type = self::TEACHABLE_TYPE;
            $teachable->classroom_id = $classroom->id;
            $teachable->created_by = auth('api')->user()->id;
            $teachable->available_at = \Carbon\Carbon::now();

            $this->assignment->teachable()->save($teachable);

            // create teachable_user
            $classroomStudents = $classroom->students()->get();

            foreach ($classroomStudents as $key => $classroomStudent) {
                $teachable_user = New \App\TeachableUser();
                $teachable_user->classroom_user_id = $classroomStudent->id;

                $teachable->teachableUsers()->save($teachable_user);
            }
        });

        return new AssignmentResource($this->assignment);
    }

    public function view()
    {
        $includes = [];
        $teachableUser = null;

        // response for student
        if (auth('api')->user()->isClassroomStudent($this->classroom)) {
            $teachableUser = $this->teachable->teachableUser($this->classroom->classroomUser());
            $includes['assignment'] = true;
        }
        else{
            $includes['assignments'] = true;
        }

        return new AssignmentResource($this->teachable, $includes, $teachableUser);
    }

    public function viewSubmission($teachableId)
    {
        $teachable = Teachable::where('id', $teachableId)->first();

        if (!$teachable) {
            throw new NotFoundException('Assignment');
        }

        return TeachableResource::collection($teachable);

    }

    public function uploadSubmission(){
        $teachableUser = $this->teachable->teachableUser($this->classroom->classroomUser());
        $teachableUser->addMediaFromRequest('submission')->toMediaCollection('submission');
        return true;
    }

    // show the list of student submission ( teacher only )
    public function listSubmission()
    {
        return new TeachableResource($this->teachable,['assignments'=>true]);
    }

    public function list($request)
    {
         return new AssignmentCollection($this->classroom->assignments()->latest()->paginate($request->perPage ?? $this->assignment->getPerPage()));
    }

    public function delete()
    {
        return $this->teachable->delete();
    }

    public function trashed()
    {
        return new AssignmentCollection($this->classroom->assignments()->onlyTrashed()->paginate($request->perPage ?? $this->assignment->getPerPage()));
    }

    public function hardDelete()
    {
        \DB::transaction(function(){
            $this->teachable->assignment->delete();
            $this->teachable->teachableUsers()->delete();
            $this->teachable->forceDelete();
        });

        return true;
    }

    private function validateAssignment(array $data){
        return Validator::make($data, [
            'available_at'   => 'nullable|date_format:d/m/Y H:i:s',
            'max_attempts' => 'integer|min:0',
            'expires_at'     => 'required|date_format:d/m/Y H:i:s',
        ]);
    }
}