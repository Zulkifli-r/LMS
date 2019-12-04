<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Assignment extends JsonResource
{
    public $includes = [];
    protected $teachableUser;

    public function __construct($collect, $includes = [], $teachableUser = null) {
        $this->includes = collect($includes);
        $this->teachableUser = $teachableUser;

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
        if ($this->resource instanceof \App\Assignment){
            return [
                'id' => $this->id,
                'title' => $this->title,
                'description' => $this->description,
                'created_by' => new Users($this->user),
            ];
        } else{
            return $this->teachable();

        }
    }

    public function includes($includes = [])
    {
        $this->includes = collect($includes);
        return $this;
    }

    private function teachable(){

        $res['assignment']['id'] = $this->assignment->id;
        $res['assignment']['title'] = $this->assignment->title;
        $res['assignment']['description'] = $this->assignment->description;
        $res['assignment']['created_by'] = new Users($this->user);

        $res['teachable']['id'] = $this->id;
        $res['teachable']['teachable_type'] = $this->teachable_type;
        $res['teachable']['created_by'] = new Users($this->user);
        $res['teachable']['available_at'] = $this->available_at;
        $res['teachable']['expires_at'] = $this->expires_at;
        $res['teachable']['due_date'] = \Carbon\Carbon::parse($this->expires_at)->diffForHumans();
        $res['teachable']['pass_tresshold'] = $this->pass_treshold;
        if ( $this->deleted_at) {
            $res['teachable']['deleted_at'] = $this->deleted_at;
            $res['teachable']['deleted_at_for_humans'] = \Carbon\Carbon::parse($this->deleted_at)->diffForHumans();
        }

        // Detail for teacher
        if (collect($this->includes)->has('assignments')) {
            $res['students'] = new TeachableUserCollection($this->teachableUsers, 'assignment');
        }

        // Detail for student
        if (collect($this->includes)->has('assignment')) {
            $res['assignment']['submission'] = new Media($this->teachableUser->getMedia('submission')->first());
        }

        return $res;
    }
}
