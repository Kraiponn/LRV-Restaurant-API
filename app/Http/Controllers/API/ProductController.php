<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Products\CreateProductRequest;
use App\Http\Requests\Products\UpdateProductRequest;
use App\Repositories\ProductRepository;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ResponseTrait;

    public function __construct(protected ProductRepository $proRepository)
    {
    }

    /************************************************************************************
    |   @Desc       Get products
    |   @Route      GET, api/products
    |   @Access     Public - (all role)
     ***********************************************************************************/
    public function getProducts(): JsonResponse
    {
        try {
            $data = $this->proRepository->findAll(request()->all());

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Get single product
    |   @Route      GET, api/products/{id}
    |   @Access     Public - (all role)
     ***********************************************************************************/
    public function getSingleProduct($id): JsonResponse
    {
        try {
            $data = $this->proRepository->findById($id);

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Created product
    |   @Route      POST, api/products
    |   @Access     Private - (Admin & Manager Role)
     ***********************************************************************************/
    public function createProduct(CreateProductRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $this->proRepository->create($request->all());

            return $this->responseSuccess($data, 201, 'Product created successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Updated product
    |   @Route      PUT, api/products/{id}
    |   @Access     Private - (Admin & Manager Role)
     ***********************************************************************************/
    public function updateProduct($id, UpdateProductRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $this->proRepository->update($id, $request->all());

            return $this->responseSuccess($data, 200, 'Product updated successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Deleted product
    |   @Route      DELETE, api/products/{id}
    |   @Access     Private - (Admin & Manager Role)
     ***********************************************************************************/
    public function deleteProduct($id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $this->proRepository->delete($id);

            return $this->responseSuccess($data, 200, 'Product deleted successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }
}
