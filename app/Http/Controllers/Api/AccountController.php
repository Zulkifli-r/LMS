<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\AccountRepository;
use Illuminate\Http\Request;

class AccountController extends Controller
{

    protected $repository;

    public function __construct(AccountRepository $accountRepository) {
        $this->repository = $accountRepository;
    }

    public function updateInfo(Request $request)
    {
        return apiResponse(200, $this->repository->updateInfo($request->all()));
    }

    public function changeAvatar(Request $request)
    {
        if ($newAvatar = $this->repository->changeAvatar($request->file('avatar'))) {
            return apiResponse(200, $newAvatar, 'Your avatar has been updated');
        }

        return apiResponse(500,null);
    }

    public function profile()
    {
        return apiResponse(200, $this->repository->profileDetails());
    }

    public function changePassword(Request $request)
    {
        return apiResponse(200, $this->repository->changePassword($request), 'Your password has been changed');
    }
}
