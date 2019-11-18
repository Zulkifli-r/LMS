<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Classes extends JsonResource
{

    public $includes;

    public function __construct($collect, $includes = []) {
        $this->includes = collect($includes);

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
        $res = [];
        $data = $this->classrooms->sortByDesc('created_at')->map(function($value){
            $value->classroom_user = $value->classroomUsers;
            return $value;
        });

        if ($this->includes->has('created')) {
            $created_classroom = $data->where('created_by', $this->id);

            $res['created']['count'] = $created_classroom->count();

            $created_classroom_data = $this->includes->has('paginate')?$created_classroom->take($this->includes['paginate']):$created_classroom;

            $res['created']['details'] = ClassroomCollection::make($created_classroom_data);

        }

        if ($this->includes->has('joined')) {

            $res['joined']['count'] = $data->count();
            $joined_data = $this->includes->has('paginate')?$data->take($this->includes['paginate']):$data;
            $res['joined']['details'] = ClassroomCollection::make($joined_data);
        }


        return $res;
    }

    public function includes(array $includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }
}
