<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Repositories\CategoryRepository;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ResponseTrait;

    public function __construct(protected CategoryRepository $catRepository)
    {
    }

    /************************************************************************************
    |   @desc       Get categories
    |   @route      GET, api/categories
    |   @access     Public - (all role)
     ***********************************************************************************/
    public function getCategories(): JsonResponse
    {
        try {
            $data = $this->catRepository->findAll(request()->all());

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @desc       Get single category
    |   @route      GET, api/categories/{id}
    |   @access     Public - (all role)
     ***********************************************************************************/
    public function getSingleCategory($id): JsonResponse
    {
        try {
            $data = $this->catRepository->findById($id);

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @desc       Created category
    |   @route      POST, api/categories
    |   @access     Private - (Admin & Manager Role)
     ***********************************************************************************/
    public function createCategory(CreateCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->catRepository->create($request->all());

            return $this->responseSuccess($data, 201, 'Category created successfully.');
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @desc       Updated category
    |   @route      PUT, api/categories/{id}
    |   @access     Private - (Admin & Manager Role)
     ***********************************************************************************/
    public function updateCategory($id, UpdateCategoryRequest $request): JsonResponse
    {
        try {
            $data = $this->catRepository->update($id, $request->all());

            return $this->responseSuccess($data, 200, 'Category updated successfully.');
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @desc       Deleted category
    |   @route      DELETE, api/categories/{id}
    |   @access     Private - (Admin & Manager Role)
     ***********************************************************************************/
    public function deleteCategory($id): JsonResponse
    {
        try {
            $data = $this->catRepository->delete($id);

            return $this->responseSuccess($data, 200, 'Category deleted successfully.');
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }
}
