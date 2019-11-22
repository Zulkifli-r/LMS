<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\ValidationException;
use App\Http\Resources\Resource;
use Illuminate\Support\Facades\Validator;

class ResourceRepository
{
    private const TEACHABLE_TYPE = 'resource';
    protected $resource;
    protected $classroom;
    protected $teachable;

    public function __construct(Classroom $classroom, $teachable = null) {
        $this->classroom = $classroom;
        $this->teachable = $teachable?Teachable::find($teachable):new \App\Teachable();
        $this->resource = new \App\Resource();
    }

    public function create($request)
    {
        $validatedData = $this->validateResourceRequest($request->all());

        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        \DB::transaction(function() use ($request) {
            // handle upload file and creating resource

            $this->resource->title = $request->title;
            $this->resource->description = $request->description;
            $this->resource->type = $request->type;
            $this->resource->save();

            call_user_func( [ $this,'handleUpload'.$request->type ], $request);

            // create teachale
            $this->teachable->fill($request->only($this->teachable->getFillable()));
            $this->teachable->teachable_type = self::TEACHABLE_TYPE;
            $this->teachable->classroom_id = $this->classroom->id;

            $this->resource->teachable()->save($this->teachable);

            // create teachable user
            $classroomStudents = $this->classroom->students()->get();
            foreach ($classroomStudents as $key => $classroomStudent) {
                $teachable_user = new \App\TeachableUser();
                $teachable_user->classroom_user_id = $classroomStudent->id;
                $this->teachable->teachableUsers()->save($teachable_user);
            }

        });

        return new Resource($this->resource);

    }

    private function handleUploadJwVideo($request){}
    private function handleUploadAudio($request){
        return $this->resource->addMediaFromRequest('Audio')->toMediaCollection('audio');
    }
    private function handleUploadFile($request){
        return $this->resource->addMediaFromRequest('File')->toMediaCollection('file');
    }
    private function handleUploadYoutubeLink($request){
        $videoId = preg_match("/(\?|&)v=([^&#]+)/",$request->YoutubeLink);
        dd($videoId);
    }
    private function handleUploadLink($request){
        // $this->resource->data =
    }

    private function validateResourceRequest($data){
        return Validator::make($data,[
                'title'                 => 'required|string',
                'description'           => 'required|string',
                'type'                  => 'required|in:JwVideo,YoutubeLink,Audio,File,Url',
                // 'resourceTypeSettings'  => 'required|json',
                // 'resourceFile'          => 'required_if:resourceType, audio',
                'Audio'                 => 'required_if:type,==,Audio|max:100000',
                'File'                  => 'required_if:type,==,File|max:101200',
                'YoutubeLink'           => 'required_if:type,==,YoutubeLink|regex:^(https?\:\/\/)?((www\.)?youtube\.com|youtu\.?be)\/.+$
                ',
            ]
        );
    }
}
