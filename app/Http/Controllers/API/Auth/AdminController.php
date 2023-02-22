<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\EditPasswordRequest;
use App\Http\Requests\EditRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Repositories\AuthRepository;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    use ResponseTrait;

    public function __construct(protected AuthRepository $authRepository)
    {
    }

    /************************************************************************************
        @desc       Get accounts
        @route      GET, api/auth/admin/users
        @access     Private - (Admin role)
     ***********************************************************************************/
    public function getAccounts(): JsonResponse
    {
        try {
            $data = $this->authRepository->fetchAccounts(request()->all());

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Get a single account
        @route      GET, api/auth/admin/users/{id}
        @access     Private - (Admin role)
     ***********************************************************************************/
    public function getSingleAccount($id): JsonResponse
    {
        try {
            $data = $this->authRepository->fetchSingleAccount($id);

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Update password
        @route      PUT, api/auth/admin/users/{id}/update-password
        @access     Private - (Admin role)
     ***********************************************************************************/
    public function updatePassword($id, EditPasswordRequest $request): JsonResponse
    {
        try {
            $data = $this->authRepository->editPassword($id, $request->all());

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Update role -> guest, member, manager and admin
        @route      PUT, api/auth/admin/users/{id}/update-role
        @access     Private - (Admin role)
     ***********************************************************************************/
    public function updateRole($id, EditRoleRequest $request): JsonResponse
    {
        try {
            $data = $this->authRepository->editRole($id, $request->all());

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }
}
