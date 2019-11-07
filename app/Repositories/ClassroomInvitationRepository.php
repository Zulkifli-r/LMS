<?php

namespace App\Repositories;

use App\Classroom;
use App\Exceptions\AuthorizationException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Invitation;
use App\Mail\ClassroomInvitation;
use App\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ClassroomInvitationRepository {

    protected $invitation;
    protected $user;

    public function __construct(User $user ) {
        $this->user = $user;
        $this->invitation = new Invitation();
    }

    public function sendPrivateInvitation(array $data)
    {
        // validate incoming data
        $validatedData = $this->validatePrivateInvitationData($data);
        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors()->toArray());
        }
        // get classroom instance
        $classroom = ClassroomRepository::getClassroomBySlug($validatedData->validated()['classroom']);

        // TODO: restrict user by role/only owner that can send an invitation

        foreach ($data['email'] as $key => $email) {

            // generate invitation token
            $invitationToken = $this->invitation->generateToken($email);
            // encrypted version of the invitation token
            $encryptedInvitationToken = sha1($invitationToken);

            $invitation = clone $this->invitation;
            $invitation->token = $encryptedInvitationToken;
            $invitation->invited_as = $data['invited_as'];
            $invitation->classroom_id = $classroom->id;
            $invitation->type = 'private';
            $invitation->email = $email;

            if ($invitation->save()) {
                Mail::to($invitation->email)->send(new ClassroomInvitation($invitation, $invitationToken));
            }

        }
    }

    public function generatePublicInvitation(array $data)
    {
        // validate incoming data
        $validatedData = $this->validatePublicInvitationData($data);
        if ($validatedData->fails()) {
            throw new ValidationException($validatedData->errors()->toArray());
        }
        // get classroom instance
        $classroom = ClassroomRepository::getClassroomBySlug($validatedData->validated()['classroom']);

         // TODO: restrict user by role/only owner that can send an invitation

        // generate invitation token
        $invitationToken = $this->invitation->generateToken();
        // encrypted version of the invitation token
        $encryptedInvitationToken = sha1($invitationToken);

        // invitation object
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

        return true;
    }

    public function joinClassroom(array $data)
    {
        $invitation = Invitation::getInvitationByToken(sha1($data['token']));

        if ($invitation == null) {
            throw new AuthorizationException();
        }

        if (\Carbon\Carbon::createFromTimestamp($data['expires']) < now() ){
            throw new ForbiddenException('Invitation link has been expired');
        }

        if ($invitation->classroom->created_by == auth('api')->user()->id) {
            throw new ForbiddenException('You\'re already the owner of this classroom');
        }

        // validate public and private link
        if ($invitation->type == 'private') {
            return $this->joiningPrivateInvitation($invitation);
        }
        else{
            return $this->joiningPublicInvitation($invitation);
        }

    }

    private function joiningPrivateInvitation($invitation){
        // get the classroom instance
        $classroom = $invitation->classroom;
        // validate invitation email against logged in user
        if ($invitation->email != $this->user->email ) {
            throw new ForbiddenException('You\'re not allowed to perform this action ');
        }
        // create classroom user
        $this->user->classrooms()->save($classroom);
         // give userclassroom role
        $classroomUser = $classroom->classroomUsers->where('user_id', $this->user->id)->first();
        $classroomUser->assignRole([$invitation->invited_as]);

        return true;
    }

    private function joiningPublicInvitation($invitation){
        // get the classroom instace
        $classroom = $invitation->classroom;
        // create classroom user
        $this->user->classrooms()->save($classroom);
        // give userclassroom role
        $classroomUser = $classroom->classroomUsers->where('user_id', $this->user->id)->first();
        $classroomUser->assignRole([$invitation->invited_as]);

        return true;
    }

    private function validatePrivateInvitationData(array $data){
        return Validator::make($data, [
                'email' => 'required',
                'email.*' => 'email',
                'classroom' => 'required',
                'invited_as' => 'required|in:student,teacher'
            ], ['email.required' => 'email is required','email.*' => ':input is not a valid email address'] );
    }

    private function validatePublicInvitationData(array $data){
        return Validator::make($data, [
            'classroom' => 'required',
            'invited_as' => 'required|in:student,teacher'
        ]);
    }
}