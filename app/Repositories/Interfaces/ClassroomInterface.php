<?php

namespace App\Repositories\Interfaces;

use App\User;
use Illuminate\Http\Request;

interface ClassroomInterface {

    public function all();
    public function with(array $relations);
    public function create(Request $data, User $user);
    public function update(Request $data, $slug);

}