<?php

namespace App\Repositories;

use App\Interfaces\ICrudRepository;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CartRepository implements ICrudRepository
{
    /*
    |------------------------------------------------------
    |   Fetch products with pagination
    |
    |   @Query      searchKey(title,slug field)
    |   @Query      page
    |   @Query      pageSize
    |   @Query      orderBy
    |   @Query      order(asc, desc)
    |
    |   @Return      Paginator
    |------------------------------------------------------
    */
    public function findAll(array $filters): Paginator
    {
        $filter = $this->getFilterData($filters);

        // $query = Product::with(['productImages' => function ($query) {
        //     $query->select(['image'])->orderBy();
        // }])
        $query = Product::orderBy($filter['orderBy'], $filter['order']);

        if (!empty($filter['searchKey'])) {
            $query = $query->where(function ($query) use ($filter) {
                $searched = '%' . $filter['searchKey'] . '%';

                $query->where('title', 'like', $searched)
                    ->orWhere('slug', 'like', $searched);
            });
        }

        // Query relate
        // $query->with(['productImages' => function ($query) {
        //     $query->select(['id', 'image'])->orderBy('id', 'desc');
        // }]);
        $query->with(['productImages']);

        $products = $query->paginate($filter['pageSize']);

        // dd($products['data']);
        // if ($products->total <= 1) {
        //     throw new Exception('Product Not Found', 404);
        // }

        return $products;
    }

    /*
    |------------------------------------------------------
    |   Fetch a single product
    |   @Param      int
    |   @Return     Product | null
    |------------------------------------------------------
    */
    public function findById($id): ?Product
    {
        $product = Product::where('id', $id)->with(['productImages' => function ($query) {
            $query->orderBy('id', 'desc');
        }])->first();

        if (!$product) {
            throw new Exception('Product not found', 404);
        }

        // dd($product);
        return $product;
    }

    /*
    |------------------------------------------------------
    |   Create new product
    |   @Param      array
    |   @Return     Product | null
    |------------------------------------------------------
    */
    public function create(array $dto): ?Product
    {
        $product = Product::create($this->getFieldToCreate($dto));

        if (!$product) {
            throw new Exception('Sorry, product created not success. Please try again.', 500);
        }

        // Store new images to storage and db
        $this->storeMultipleImages($dto['image'], $product['id']);

        DB::commit();
        return $product;
    }

    /*
    |------------------------------------------------------
    |   Update product by id
    |   @Param      int
    |   @Param      array
    |   @Return     Product | null
    |------------------------------------------------------
    */
    public function update($id, array $dto): ?Product
    {
        $product = Product::find($id);

        if (!$product) {
            throw new Exception('Product not found', 404);
        }

        $product['title'] = $dto['title'] ?? $product['title'];
        $product['slug'] = $dto['title'] ? Str::slug($dto['title'], '-') : $product['slug'];
        $product['description'] = $dto['description'] ?? $product['description'];
        $product['unit_price'] = $dto['unit_price'] ?? $product['unit_price'];
        $product['in_stock'] = $dto['in_stock'] ?? $product['in_stock'];
        $product['category_id'] = $dto['category_id'] ?? $product['category_id'];
        $product->save();

        if (!empty($dto['image'])) {
            // Get old product images
            $oldProdImgs = ProductImage::where('product_id', $id)->get();

            // Remove old product image from db
            ProductImage::where('product_id', $id)->delete();

            // Remove images from storage
            if (count($oldProdImgs) > 0) {
                $this->removeMultipleImages($oldProdImgs);
            }

            // Store new images to storage and db
            $this->storeMultipleImages($dto['image'], $id);
        }

        DB::commit();
        return $product;
    }

    /*
    |------------------------------------------------------
    |   Delete product by Id
    |   @Param      int
    |   @Return     Product | null
    |------------------------------------------------------
    */
    public function delete($id): ?Product
    {
        $product = Product::with('images')->where('id', $id)->first();

        if (!$product) {
            throw new Exception('Product not found', 404);
        }

        // Remove product
        Product::where('id', $id)->delete();

        if (count($product->images) > 0) {
            $this->removeMultipleImages($product->images);
        }

        DB::commit();
        return $product;
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
            'searchKey' => '',
            'page' => 1,
            'pageSize' => 10
        ];

        return array_merge($defaultValues, $filterData);
    }

    /*
    |------------------------------------------------------
    |   Generate fields for created product.
    |   @Param      array
    |   @Return     array
    |------------------------------------------------------
    */
    private function getFieldToCreate(array $dto): array
    {
        return [
            'title' => $dto['title'],
            'slug' => Str::slug($dto['title'], '-'),
            'description' => $dto['description'],
            'unit_price' => $dto['unit_price'],
            'in_stock' => $dto['in_stock'],
            'category_id' => $dto['category_id'],
        ];
    }

    /*
    |------------------------------------------------------
    |   Store new multi-images to storage & Db
    |   @Param      array
    |   @Param      int
    |   @Return     void
    |------------------------------------------------------
    */
    private function storeMultipleImages(array $images, int $productId = 1): void
    {
        // Loop to store new images and store data to ProductImages table
        foreach ($images as $file) {
            $fileExtension = $file->getClientOriginalExtension();
            $fileName = uniqid() . '-' . time() . '.' . $fileExtension;

            // Store image
            Storage::disk('public')->put('upload/products/' . $fileName, file_get_contents($file));

            // Store image name to db
            $picture = new ProductImage();
            $picture->product_id = $productId;
            $picture->image = $fileName;
            $picture->save();
        }
    }

    /*
    |------------------------------------------------------
    |   Remove multi-images from storage
    |   @Param      array
    |   @Return     void
    |------------------------------------------------------
    */
    private function removeMultipleImages(object $images)
    {
        // Loop for remove old images from storage
        for ($i = 0; $i < count($images); $i++) {
            Storage::disk('public')->delete(
                './upload/products/' . $images[$i]->image
            );
        }
    }
}
