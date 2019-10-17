<?php

namespace App\Repositories\Traits;

trait RequestParameter
{
    public function filter(string $filter)
    {
        # code...
    }

    public function sort($object, string $sort)
    {
        // $object->sort
    }

    public function fields()
    {
        # code...
    }

    public function page_limit(int $limit)
    {

    }

    public function page_offset(int $limit)
    {

    }
}