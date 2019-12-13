<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassroomUserStatus extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $res = [];
        $user = auth('api')->user();
        $res['user'] = new Users($user);

        $classroomUser = $user->classroomUsers->where('classroom_id', $this->id);

        if ($classroomUser->first()) {
            $classroomUser = $classroomUser->first()->roles->first()->name;
            $res['classroom_status'] = $user->isClassroomOwner($this) ? $classroomUser.'/owner':$classroomUser;
        }

        return $res;



    }
}
