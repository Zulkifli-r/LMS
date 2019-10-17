<?php

namespace App\Repositories\Interfaces;

use App\User;

interface ClassroomInterface {

    public function all();
    public function with(array $relations);
    public function create(array $data, User $user);
    public function update();

}