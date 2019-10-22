<?php

namespace App\Repositories;

use App\Exceptions\AuthorizationException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Invitation;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ClassroomInvitationRepository {

    protected $invitation;

    public function __construct() {
        $this->invitation = new Invitation();
    }

    public function sendPrivateInvitation(array $data)
    {
        $validatedData = $this->validatePrivateInvitationData($data);
        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors()->toArray());
        }

        dd($validatedData);

    }

    public function generatePublicInvitation(array $data)
    {
        $validatedData = $this->validatePublicInvitationData($data);
        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors()->toArray());
        }

        $classroom = ClassroomRepository::getClassroomBySlug($validatedData->validated()['classroom']);
        $invitationToken = $this->invitation->generateToken();
        $encryptedInvitationToken = sha1($invitationToken);

        $invitation = $this->invitation;
        $invitation->token = $encryptedInvitationToken;
        $invitation->invited_as = $validatedData->validated()['invited_as'];
        $invitation->classroom_id = $classroom->id;
        $invitation->type = 'public';

        if( $invitation->save() ){
            return URL::temporarySignedRoute('fe-invitation-route', now()->addDays(1),[
                'classroom' => $classroom->slug,
                'token' => $invitationToken
            ] );

        }

    }

    public function joinClassroom(array $data)
    {
        $invitation = Invitation::getInvitationByToken(sha1($data['token']));

        if ($invitation == null) {
            throw new AuthorizationException();
        }

        if (\Carbon\Carbon::createFromTimestamp($data['expires'])){
            throw new ForbiddenException('Invitation link has been expired');
        }

        if (! hash_equals((string) $request->id, (string) $request->user()->getKey())) {
            throw new AuthorizationException();
        }

        if (! hash_equals((string) $request->hash, sha1($request->user()->getEmailForVerification()))) {
            throw new AuthorizationException;
        }


    }

    private function validatePrivateInvitationData(array $data){
        return Validator::make($data, [
                'email.*' => 'email',
                'classroom' => 'required',
                'invited_as' => 'required|in:student,teacher'
            ], ['email.*' => ':input is not a valid email address'] );
    }

    private function validatePublicInvitationData(array $data){
        return Validator::make($data, [
            'classroom' => 'required',
            'invited_as' => 'required|in:student,teacher'
        ]);
    }
}