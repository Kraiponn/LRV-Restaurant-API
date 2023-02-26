<?php

namespace App\Repositories;

use App\Interfaces\ICrudRepository;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRepository implements ICrudRepository
{
    /*
    |------------------------------------------------------
    |   Fetch orders with pagination
    |
    |   @Query      page
    |   @Query      pageSize
    |   @Query      orderBy
    |   @Query      order(asc, desc)
    |   @Query      order_date
    |   @Query      shipping_date
    |   @Query      status              [pendding, prepare, shipping, finish]
    |
    |   @Return      Paginator
    |------------------------------------------------------
    */
    public function findAll(array $filters): Paginator
    {
        $filter = $this->getFilterData($filters);

        $query = Order::with('products');

        $query = $query->orderBy($filter['orderBy'], $filter['order']);

        if (!empty($filter['status'])) {
            $query->where('status', $filter['status']);
        }

        $query = !empty($filter['order_date'])
            ? $query->where('order_date', $filter['order_date'])
            : $query;

        $query = !empty($filter['shipping_date'])
            ? $query->where('shipping_date', $filter['shipping_date'])
            : $query;

        $categories = $query->paginate($filter['pageSize']);

        return $categories;
    }

    /*
    |------------------------------------------------------
    |   Find a single order by id - (This method no using)
    |   @Param      int
    |   @Return     Order | null
    |------------------------------------------------------
    */
    public function findById($oId): ?Order
    {
        $order = Order::with('products')->where('id', $oId)->with('orderProducts')->first();

        if (!$order) {
            throw new Exception('Order Not Found', 404);
        }

        return $order;
    }

    /*
    |------------------------------------------------------
    |   Create new order
    |   @Param      array
    |   @Return     Order | null
    |------------------------------------------------------
    */
    public function create(array $dto): ?Order
    {
        $uId = Auth::user()->id;

        // Add new order to db
        $order = new Order();
        $order['user_id'] = $uId;
        $order['location'] = $dto['location'];
        $order['table_no'] = $dto['table_no'];
        $order['order_date'] = $dto['order_date'];
        $order['shipping_date'] = $dto['shipping_date'];
        $order->save();

        if (!$order || empty($order)) {
            throw new Exception('Invalid created order', 400);
        }

        $productIds = array();
        foreach ($dto['products'] as $product) {
            // Store product_id to array for destroy cart
            array_push($productIds, $product['product_id']);

            // Assign order to pivot table
            $order->products()->attach([
                $product['product_id'] => ['quantity' => $product['quantity']]
            ]);
        }

        // Destroy data from cart
        User::find($uId)->products()->detach($productIds);

        DB::commit();
        return $order;
    }

    /*
    |------------------------------------------------------
    |   Update order by id
    |   @Param      int
    |   @Param      array
    |   @Return     Order | null
    |------------------------------------------------------
    */
    public function update($id, array $dto): ?Order
    {
        $order = Order::find($id);

        if (!$order) {
            throw new Exception('Order not found', 404);
        }

        $order->status = $dto['status'] ?? $order['status'];
        $order->location = $dto['location'] ?? $order['location'];
        $order->order_date = $dto['order_date'] ?? $order['order_date'];
        $order->shipping_date = $dto['shipping_date'] ?? $order['shipping_date'];
        $order->table_no = $dto['table_no'] ?? $order['table_no'];
        $order->save();

        DB::commit();
        return $order;
    }

    /*
    |------------------------------------------------------
    |   Delete order by id
    |   @Param      int
    |   @Return     Order | null
    |------------------------------------------------------
    */
    public function delete($oId): ?Order
    {
        $order = Order::with('products')->where('id', $oId)->first();

        if (!$order) {
            throw new Exception('Order not found', 404);
        }

        if ($order['status'] == 'shipping' || $order['status'] == 'finish') {
            throw new Exception('Can not cancel the order after shipping or finish state', 400);
        }

        $order->delete();

        DB::commit();
        return $order;
    }


    //#################################################################################//
    //                 >>>>>>>        Helper Functions        <<<<<<<                  //
    //#################################################################################//

    // Filter data for search orders
    private function getFilterData(array $filterData): array
    {
        $defaultValues = [
            'orderBy' => 'id',
            'order' => 'desc',
            'page' => 1,
            'pageSize' => 10,
            'order_date' => now(),
            'shipping_date' => now(),
            'status' => ''
        ];

        return array_merge($defaultValues, $filterData);
    }
}
