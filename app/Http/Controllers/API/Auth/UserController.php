<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\UpdateAccountRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Repositories\AuthRepository;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    use ResponseTrait;

    public function __construct(protected AuthRepository $authRepository)
    {
    }

    /************************************************************************************
        @desc       Get profile
        @route      GET, api/auth/profile
        @access     Private - (Owner account)
     ***********************************************************************************/
    public function account()
    {
        try {
            $user = request()->user();

            return $this->responseSuccess($this->authRepository->getAccount($user->id));
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Register new member
        @route      POST, api/auth/register
        @access     Public
     ***********************************************************************************/
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->authRepository->register($request->all());

            return $this->responseSuccess($data, 201, 'User registerd successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Login to access app
        @route      POST, api/auth/login
        @access     Private - (Owner account & Admin)
     ***********************************************************************************/
    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authRepository->login($request->all());

            return $this->responseSuccess($data, 200, 'User logged in successfully.');
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Logout from application
        @route      GET, api/auth/logout
        @access     Private - (Owner account & Admin)
     ***********************************************************************************/
    public function logout()
    {
        try {
            $data = $this->authRepository->logout();

            return $this->responseSuccess($data, 200, 'User logged out successfully.');
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Update account
        @route      POST, api/auth/update-account
        @access     Private - (Owner account & Admin)
     ***********************************************************************************/
    public function updateAccount(UpdateAccountRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->authRepository->updateAccount($request->all());

            return $this->responseSuccess($data, 200, 'Account updated successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Update password
        @route      PUT, api/auth/update-password
        @access     Private - (Owner account & Admin)
     ***********************************************************************************/
    public function updatePassword(UpdatePasswordRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->authRepository->updatePassword($request->all());

            return $this->responseSuccess($data, 200, 'Password updated successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Remove an account
        @route      DELETE, api/auth/remove-account
        @access     Private - (Owner account & Admin)
     ***********************************************************************************/
    public function removeAccount()
    {
        try {
            DB::beginTransaction();
            $data = $this->authRepository->removeAccount();

            return $this->responseSuccess($data, 200, 'Account removed successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }
}
