<?php

namespace App\Auth\Interfaces\Auth;

interface IUserRegister
{
    public $name;
    public $email;
    public $password;
    public $passwordConfirmation;
}
