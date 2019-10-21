<?php

namespace App\Repositories\Interfaces;

use Illuminate\Http\UploadedFile;

interface AccountInterface{

    public function changeAvatar(UploadedFile $newAvatar);
    public function updateInfo(array $data);
}