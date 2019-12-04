<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class Teachable extends JsonResource
{

    protected $includes = [];
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
        $res = [];
        $res['id'] = $this->id;
        $res['teachable_type'] = $this->teachable_type;
        $res['max_attempts'] = $this->max_attempts_count;
        if (collect($this->includes)->has('assignment')) {
            $res['assignment'] = new Assignment($this->assignment);
            $res['submission'] = new Media($this->teachableUser->getMedia('submission')->first());
        }

        if (collect($this->includes)->has('assignments')) {
            $res['assignment'] = new Assignment($this->assignment);
            $res['students'] = TeachableUser::collection($this->teachableUsers, 'assignment');
        }

        $res['created_by'] = new Users($this->user);
        $res['available_at'] = $this->available_at;
        $res['expires_at'] = $this->expires_at;
        $res['due_date'] = Carbon::parse($this->expires_at)->diffForHumans();
        $res['pass_tresshold'] = $this->pass_treshold;

        return $res;

    }
}
