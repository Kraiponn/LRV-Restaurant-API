<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface ICategoryRepository
{
    public function findCateogries(array $filters): Paginator;
    public function findById($id): object | null;
    public function create(array $dto): object | null;
    public function update($id, array $dto): object | null;
    public function delete($id): object | null;
}
