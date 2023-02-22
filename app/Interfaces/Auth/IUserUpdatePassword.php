<?php

namespace App\Auth\Interfaces\Auth;

interface IUserUpdatePassword
{
    public $currentPassword;
    public $newPassword;
}
