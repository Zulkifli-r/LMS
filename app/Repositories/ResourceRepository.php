<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\BadRequestException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Http\Resources\Resource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\TeachableMediaCollection;
use App\Teachable;
use Illuminate\Support\Facades\Validator;
use Jwplayer\JwplatformAPI;

class ResourceRepository
{
    private const TEACHABLE_TYPE = 'resource';
    protected $resource;
    protected $classroom;
    protected $teachable;

    public function __construct(Classroom $classroom, $teachable = null) {
        $this->classroom = $classroom;
        $this->teachable = $teachable?Teachable::findOrNotFound($teachable):null;

        if ($this->teachable && $this->teachable->teachable_type != 'resource') {
            throw new NotFoundException('Resource');
        }

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

    public function list($request)
    {
        $perPage = $request->has('perPage')?$request->perPage:$this->resource->getPerPage();
        $data = $this->classroom->resources()->latest()->paginate($perPage);

        return new TeachableMediaCollection($data);
    }

    public function details()
    {
        return new Resource($this->teachable);
    }

    public function delete()
    {
        return $this->teachable->delete();
    }

    public function trashed($request)
    {
        $resource = $this->classroom->resources()->onlyTrashed()->paginate($request->perPage ?? $this->resource->getPerPage());

        return new ResourceCollection($resource);
    }

    public function hardDelete()
    {
        // force delete resource and clear media collection
        \DB::transaction(function(){
            $this->teachable->resource->clearMediaCollection();
            $this->teachable->resource->forceDelete();
            $this->teachable->teachableUsers()->delete();
            $this->teachable->forceDelete();
        });

        return true;
    }

    private function handleUploadJwVideo($request){
        // upload video to temporary location
        $this->resource->addMediaFromRequest('JwVideo')->toMediaCollection('tmpVideo');
        $target_file = $this->resource->getMedia('tmpVideo')->first()->getPath();

        $jwplatform_api = new JwplatformAPI(env('JWPLAYER_KEY'),env('JWPLAYER_SECRET'));
        $video['title'] = $this->resource->title;
        $video['description'] = $this->resource->description;

        $create_response = json_encode($jwplatform_api->call('/videos/create', $video));
        $decoded = json_decode(trim($create_response), TRUE);
        $upload_link = $decoded['link'];

        $upload_response = $jwplatform_api->upload($upload_link, $target_file);

        if ($upload_response['status'] != 'ok') {
            throw new BadRequestException($upload_response['message']);
        }

        $this->resource->data = ['videoId' => $upload_response['media']['key'], 'playerId' => env('JWPLAYER_PLAYER_KEY')];
        $this->resource->save();

        $this->resource->clearMediaCollection('tmpVideo');

    }
    private function handleUploadAudio($request){
        return $this->resource->addMediaFromRequest('Audio')->toMediaCollection('audio');
    }
    private function handleUploadFile($request){
        return $this->resource->addMediaFromRequest('File')->toMediaCollection('file');
    }
    private function handleUploadYoutubeLink($request){
        parse_str( parse_url( $request->YoutubeLink, PHP_URL_QUERY ), $url);
        $url = collect($url);

        if (!$url->has('v')) {
            throw new NotFoundException('Youtube video');
        }

        $this->resource->data = ['videoId' => $url['v']];
        $this->resource->save();
    }
    private function handleUploadLink($request){
        $this->resource->data = ['link' => $request->Link];
        $this->resource->save();
    }

    private function validateResourceRequest($data){
        return Validator::make($data,[
                'title'                 => 'required|string',
                'description'           => 'required|string',
                'type'                  => 'required|in:JwVideo,YoutubeLink,Audio,File,Link',
                // 'resourceTypeSettings'  => 'required|json',
                // 'resourceFile'          => 'required_if:resourceType, audio',
                'Audio'                 => 'required_if:type,==,Audio|max:100000',
                'File'                  => 'required_if:type,==,File|max:101200',
                'YoutubeLink'           => 'required_if:type,==,YoutubeLink',
                'Link'                  => 'required_if:type,==,Link',
                'JwVideo'               => 'required_if:type,==,JwVideo'
            ]
        );
    }
}
