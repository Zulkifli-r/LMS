<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Account extends JsonResource
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
        $data = $this->classrooms->map(function($value){
            $value->classroom_user = $value->classroomUsers;
            return $value;
        });

        $created_classroom = $data->where('created_by', $this->id);

        $res['user'] = new Users($this);
        $res['class_count'] = $data->count();
        $res['created_class']['count'] = $created_classroom->count();
        $res['created_class']['details'] = ClassroomCollection::make($created_classroom->take(5))
                                            ->includes(
                                                [
                                                    'students'=>true,
                                                     'students_count' => true
                                                ]);
        $res['my_class']['count'] = $data->count();
        $res['my_class']['details'] = ClassroomCollection::make($data->take(5));
        $res['last_update'] = null;

        return $res;
    }
}
