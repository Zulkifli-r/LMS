<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ClassroomInvitationRepository;
use Illuminate\Http\Request;

class InvitationController extends Controller
{
    protected $repository;

    public function __construct(ClassroomInvitationRepository $invitation) {
        $this->repository= $invitation;
    }

    public function generatePublicInvitation(Request $request)
    {
        return apiResponse(200, $this->repository->generatePublicInvitation($request->all()));
    }

    public function sendPrivateInvitation(Request $request)
    {
        return apiResponse(200,$this->repository->sendPrivateInvitation($request->all()), 'We\'ve send invitation link to the following email account');
    }

    public function joinClassroom(Request $request)
    {
        return apiResponse(200, $this->repository->joinClassroom($request->all()));
    }
}
