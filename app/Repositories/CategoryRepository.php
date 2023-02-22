<?php

namespace App\Repositories;

use App\Interfaces\ICategoryRepository;
use App\Models\Category;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;

class CategoryRepository implements ICategoryRepository
{
    /*
    |------------------------------------------------------
    |   Fetch categories with pagination
    |
    |   @Query      searchKey(title field)
    |   @Query      page
    |   @Query      pageSize
    |   @Query      orderBy
    |   @Query      order(asc, desc)
    |
    |   @Return      Paginator
    |------------------------------------------------------
    */
    public function findCateogries(array $filters): Paginator
    {
        $filter = $this->getFilterData($filters);

        $query = Category::orderBy($filter['orderBy'], $filter['order']);

        if (!empty($filter['searchKey'])) {
            $query = $query->where(function ($query) use ($filter) {
                $searched = '%' . $filter['searchKey'] . '%';

                $query->where('title', 'like', $searched);
            });
        }

        return $query->paginate($filter['pageSize']);
    }

    /*
    |------------------------------------------------------
    |   Fetch a single category
    |   @Param      int
    |   @Return     Category | null
    |------------------------------------------------------
    */
    public function findById($id): ?Category
    {
        $category = Category::find($id);

        if (!$category) {
            throw new Exception('Category not found', 404);
        }

        return $category;
    }

    /*
    |------------------------------------------------------
    |   Create new category
    |   @Param      array
    |   @Return     Category | null
    |------------------------------------------------------
    */
    public function create(array $dto): ?Category
    {
        $category = new Category();

        $category->title = $dto['title'];
        $category->description = $dto['description'];
        $category->save();

        return $category;
    }

    /*
    |------------------------------------------------------
    |   Update category by id
    |   @Param      int
    |   @Param      array
    |   @Return     Category | null
    |------------------------------------------------------
    */
    public function update($id, array $dto): ?Category
    {
        $category = Category::find($id);

        if (!$category) {
            throw new Exception('Category not found', 404);
        }

        $category->title = $dto['title'];
        $category->description = $dto['description'];
        $category->save();

        return $category;
    }

    /*
    |------------------------------------------------------
    |   Fetch a single category
    |   @Param      int
    |   @Return     Category | null
    |------------------------------------------------------
    */
    public function delete($id): ?Category
    {
        $category = Category::where('id', $id);

        if (!$category) {
            throw new Exception('Category not found', 404);
        }

        $category->delete();

        return $category;
    }


    //#################################################################################//
    //                 >>>>>>>        Helper Functions        <<<<<<<                  //
    //#################################################################################//

    // Filter data for search categories
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
}
