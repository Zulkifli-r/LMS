<?php

namespace App\Repositories;

use App\Exceptions\ValidationException;
use App\Repositories\Interfaces\AccountInterface;
use App\User;
use Illuminate\Support\Facades\Validator;

class AccountRepository implements AccountInterface
{
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function changeAvatar($newAvatar)
    {
        $this->user->addMedia($newAvatar)->toMediaCollection('avatar');
        return count($this->user->getMedia('avatar')) ? asset($this->user->getMedia('avatar')->first()->getUrl()) : null;
    }

    public function updateInfo(array $data)
    {
        $validatedData = $this->validateAccountInfo($data);

        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        $this->user->fill($validatedData->validated());
        $this->user->save();

        return $this->user;
    }

    private function validateAccountInfo(array $data){
        return Validator::make($data, [
            'name' => 'string|required|max:255',
        ]);
    }
}
