<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Orders\CreateOrderRequest;
use App\Http\Requests\Orders\UpdateOrderRequest;
use App\Repositories\OrderRepository;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    use ResponseTrait;

    public function __construct(protected OrderRepository $orderRepository)
    {
    }

    /************************************************************************************
    |   @Desc       Get orders
    |   @Route      GET, api/orders
    |   @Access     Public - (all role)
     ***********************************************************************************/
    public function getOrders(): JsonResponse
    {
        try {
            $data = $this->orderRepository->findAll(request()->all());

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Get single order
    |   @Route      GET, api/orders/{oId}
    |   @Access     Public - (all role)
     ***********************************************************************************/
    public function getSingleOrder($oId): JsonResponse
    {
        try {
            $data = $this->orderRepository->findById($oId);

            return $this->responseSuccess($data);
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Created order
    |   @Route      POST, api/orders
    |   @Access     Private
     ***********************************************************************************/
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $this->orderRepository->create($request->all());

            return $this->responseSuccess($data, 201, 'Order created successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Updated order by order id
    |   @Route      PUT, api/orders/{id}
    |   @Access     Private - (Only Admin or Manager Role)
     ***********************************************************************************/
    public function updateOrder($oId, UpdateOrderRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $this->orderRepository->update($oId, $request->all());

            return $this->responseSuccess($data, 200, 'Order updated successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
    |   @Desc       Deleted order
    |   @Route      DELETE, api/orders/{id}
    |   @Access     Private - (Owner order or manager & admin)
     ***********************************************************************************/
    public function deleteOrder($oId): JsonResponse
    {
        try {
            DB::beginTransaction();
            $data = $this->orderRepository->delete($oId);

            return $this->responseSuccess($data, 200, 'Order deleted successfully.');
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }
}
