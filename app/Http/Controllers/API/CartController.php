<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cart\CreateCartRequest;
use App\Repositories\CartRepository;
use App\Traits\ResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    use ResponseTrait;

    public function __construct(protected CartRepository $cartRepository)
    {
    }

    /************************************************************************************
        @desc       Get products on cart
        @route      GET, api/cart/products
        @access     Private - (Owner account)
     ***********************************************************************************/
    public function getCart(Request $request)
    {
        try {
            $uId = $request->user()->id;
            $sfilter = $request->all();

            return $this->responseSuccess($this->cartRepository->findProductsOnCart($uId, $sfilter));
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Add new product to cart
        @route      POST, api/cart/products/add
        @access     Private - (Owner account)
     ***********************************************************************************/
    public function add(CreateCartRequest $request)
    {
        try {
            DB::beginTransaction();

            $uId = Auth::user()->id;
            $data = $request->all();

            return $this->responseSuccess($this->cartRepository->add($data, $uId));
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Increase product to cart
        @route      POST, api/cart/products/increment
        @access     Private - (Owner account)
     ***********************************************************************************/
    public function increaseProductsToCart(CreateCartRequest $request)
    {
        try {
            DB::beginTransaction();

            $uId = Auth::user()->id;
            $data = $request->all();

            return $this->responseSuccess($this->cartRepository->increase($data, $uId));
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Decrease product from cart
        @route      POST, api/cart/products/decrement
        @access     Private - (Owner account)
     ***********************************************************************************/
    public function decreaseProductsFromCart(CreateCartRequest $request)
    {
        try {
            DB::beginTransaction();

            $uId = Auth::user()->id;
            $data = $request->all();

            return $this->responseSuccess($this->cartRepository->decrease($data, $uId));
        } catch (Exception $ex) {
            DB::rollBack();

            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }

    /************************************************************************************
        @desc       Destroy products from cart by specifiy cart id
        @route      DELETE, api/cart/{cId}/products/{pId}
        @access     Private - (Owner account)
     ***********************************************************************************/
    public function destroyProducts($cId, $pId)
    {
        try {
            return $this->responseSuccess($this->cartRepository->destroy($cId, $pId));
        } catch (Exception $ex) {
            return $this->responseError(
                is_array($ex->getMessage()) ? $ex->getMessage() : [$ex->getMessage()],
                $ex->getCode()
            );
        }
    }
}
