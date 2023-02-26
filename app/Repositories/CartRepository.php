<?php

namespace App\Repositories;

use App\Models\ProductUser;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartRepository
{
    /*
    |------------------------------------------------------
    |   Fetch products on your cart with pagination
    |
        @Param      int                 user_id
    |   @Query      page                default = 1
    |   @Query      pageSize            default = 10
    |   @Query      orderBy             default = id
    |   @Query      order(asc, desc)    default = desc
    |
    |   @Return      Paginator
    |------------------------------------------------------
    */
    public function findProductsOnCart($uId, array $filters): Paginator
    {
        $filter = $this->getFilterData($filters);

        // $query = User::find($uId);
        $query = User::where('id', $uId);

        // Query Relation
        $query = $query->with('products');      // Using with User::where
        // $query = $query->products();         // Using with User::find

        // Order By ? ?
        $query = $query->orderBy($filter['orderBy'], $filter['order']);

        return $query->paginate($filter['pageSize']);
    }

    /*
    |------------------------------------------------------
    |   Add product to cart
    |   @Param      array
    |   @Param      int
    |   @Return     array
    |------------------------------------------------------
    */
    public function add(array $dto, $uId): array
    {
        $isProductActive = ProductUser::where('user_id', $uId)
            ->where('product_id', $dto['product_id'])
            ->first();

        // This product active on cart
        if ($isProductActive) {
            // Update product quantity on cart
            $isProductActive['quantity'] = $isProductActive['quantity'] + $dto['quantity'];
            // Save
            $isProductActive->save();

            DB::commit();
            return $this->getResponse(
                $isProductActive['id'],
                $uId,
                $dto['product_id'],
                $isProductActive['quantity']
            );
        }

        $user = User::find($uId);
        if (!$user) {
            throw new Exception('User Not Found', 404);
        }

        $pId = $dto['product_id'];
        $quantity = $dto['quantity'];

        $result = ProductUser::create([
            'user_id' => $uId,
            'product_id' => $pId,
            'quantity' => $quantity
        ]);

        DB::commit();
        return $this->getResponse(
            'hello',
            $result['id'],
            $uId,
            $result['product_id'],
            $result['quantity']
        );
    }

    /*
    |------------------------------------------------------
    |   Increase product to cart
    |   @Param      array
    |   @Param      int
    |   @Return     Array
    |------------------------------------------------------
    */
    public function increase(array $dto, $uId): array
    {
        $pId = $dto['product_id'];
        $quantity = $dto['quantity'];

        $cart = ProductUser::where('user_id', $uId)->where('product_id', $pId)->first();

        if (!$cart) {
            throw new Exception('Product not found on cart', 404);
        }

        // Update product quantity on cart
        $cart->quantity = $cart['quantity'] + $quantity;
        // Save
        $cart->save();

        DB::commit();
        return $this->getResponse(
            $cart['id'],
            $uId,
            $cart['product_id'],
            $cart['quantity']
        );
    }

    /*
    |------------------------------------------------------
    |   Decrement product from cart
    |   @Param      array
    |   @Param      int
    |   @Return     Array
    |------------------------------------------------------
    */
    public function decrease(array $dto, $uId): array
    {
        $pId = $dto['product_id'];
        $quantity = $dto['quantity'];

        $cart = ProductUser::where('user_id', $uId)->where('product_id', $pId)->first();

        if (!$cart) {
            throw new Exception('Product not found', 404);
        }

        if ($cart['quantity'] > 1) {
            // Update product quantity on cart
            $cart->quantity = $cart['quantity'] - $quantity;
            // Save
            $cart->save();

            $quantity = $cart['quantity'];
        } else {
            ProductUser::where('id', $cart->id)->delete();
            $quantity = 0;
        }

        DB::commit();
        return $this->getResponse($cart['id'], $uId, $pId, $quantity);
    }

    /*
    |------------------------------------------------------
    |   Destroy product from cart by id of product_users
    |   @Param      int
    |   @Return     array
    |------------------------------------------------------
    */
    public function destroy($cId, $pId)
    {
        $uId = Auth::user()->id;

        if (!ProductUser::where('id', $cId)->delete()) {
            throw new Exception('Product not found for delete', 404);
        }

        DB::commit();

        return $this->getResponse($cId, $uId, $pId, 0);
    }


    //#################################################################################//
    //                 >>>>>>>        Helper Functions        <<<<<<<                  //
    //#################################################################################//

    /*
    |------------------------------------------------------
    |   Generate data for filters product.
    |   @Param      array
    |   @Return     array
    |------------------------------------------------------
    */
    private function getFilterData(array $filterData): array
    {
        $defaultValues = [
            'orderBy' => 'id',
            'order' => 'desc',
            'page' => 1,
            'pageSize' => 10
        ];

        return array_merge($defaultValues, $filterData);
    }

    /*
    |------------------------------------------------------
    |   Generate response for decrease method
    |   @Param      int
    |   @Param      int
    |   @Param      int
    |   @Param      int
    |   @Return     array
    |------------------------------------------------------
    */
    private function getResponse($id, $uId, $pId, $quantity): array
    {
        return [
            'id' => $id,
            'user_id' => $uId,
            'product_id' => $pId,
            'quantity' => $quantity,
        ];
    }
}
