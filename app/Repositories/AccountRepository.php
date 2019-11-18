<?php

namespace App\Repositories;

use App\Exceptions\ValidationException;
use App\Http\Resources\Account;
use App\Repositories\Interfaces\AccountInterface;
use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccountRepository implements AccountInterface
{
    protected $user;

    public function __construct(User $user) {
        $this->user = $user;
    }

    public function changeAvatar($newAvatar)
    {
        $this->user->clearMediaCollection('avatar');
        $this->user->addMedia($newAvatar)->toMediaCollection('avatar');
        return count($this->user->getMedia('avatar')) ? asset($this->user->getMedia('avatar')->sortByDesc('created_at')->first()->getFullUrl()) : null;
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

    public function profileDetails()
    {
        return new Account($this->user);
    }

    public function changePassword($data){
        $validatedData = $this->validatePasswordResetRequest($data->all());

        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors());
        }

        if (!Hash::check($data->old_password, $this->user->password)) {

            throw new ValidationException(['old_password' => ['Old password did not match']]);
        }

        $this->user->password = Hash::make($data->new_password);
        $this->user->save();

        return true;
    }

    private function validatePasswordResetRequest($data){
        return Validator::make($data, [
            'old_password' => 'required|string',
            'new_password' => 'required|confirmed|string|min:8',
        ]);
    }

    private function validateAccountInfo(array $data){
        return Validator::make($data, [
            'name' => 'string|required|max:255',
        ]);
    }
}
