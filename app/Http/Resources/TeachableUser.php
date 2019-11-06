<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeachableUser extends JsonResource
{
    protected $className;

    public function __construct($collect,$className) {
        $this->className = $className;
        parent::__construct($collect);
    }

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res['user'] = new Users($this->classroomUser->user);

        $media = $this->getMedia('submission');
        $res['submission'] = $media->first() ? $media->first()->getUrl():null;

        return $res;
    }
}
