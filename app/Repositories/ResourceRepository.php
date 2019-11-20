<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;

class ResourceRepository
{
    protected $model;
    protected $classroom;
    protected $teachable;

    public function __construct(Classroom $classroom, $teachable = null) {
        $this->classroom = $classroom;
        $this->teachable = $teachable?Teachable::find($teachable):null;
        $this->model = new \App\Resource();
    }

    public function create($request)
    {
        $validatedData = $this->validateResourceRequest($request->all());

        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        dd($request->all());

    }

    private function handleUploadJwPlayerVideo($request){}
    private function handleUploadAudio($request){}
    private function handleUploadDocument($request){}
    private function handleUploadYoutubeLink($request){}
    private function handleUploadLink($request){}

    private function validateResourceRequest($data){
        return Validator::make($data,[
                'title'                 => 'required|string',
                'description'           => 'required|string',
                'type'                  => 'required|in:jwvideo,youtubevide,audio,documents,url,linkvideo',
                // 'resourceTypeSettings'  => 'required|json',
                // 'resourceFile'          => 'required_if:resourceType, audio',
                'audio'          => 'required_if:type,==,audio|max:20000',
                'file'                  => 'file|max:51200',
            ]
        );
    }
}
