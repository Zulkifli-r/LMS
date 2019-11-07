<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\TagRepository;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $repository;

    public function __construct(TagRepository $tag) {
        $this->repository = $tag;
    }

    public function getAllTags()
    {
        return apiResponse(200, $this->repository->getListTags());
    }

    public function autocomplete(Request $request)
    {
        return apiResponse(200, $this->repository->atocomplete($request));
    }
}
