<?php

namespace App\Repositories;

class ResourceRepository
{
    protected $model ;

    public function __construct() {
        $this->model = new \App\Resource();
    }

    public function create()
    {

    }
}
