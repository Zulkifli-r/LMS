<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = ['type' , 'invited_as', 'classroom_id', 'email', 'token'];

    public function generateToken($email = null)
    {
        if ($email) {
            return substr(md5(rand(0,18).$email.time()),0,32);
        }

        return substr(md5(rand(0,18).time()),0,32);
    }

    public static function getInvitationByToken($token)
    {
        return (new self)->where('token', $token)->first() ;
    }
}
