<?php

namespace App\Interfaces;

use Illuminate\Contracts\Pagination\Paginator;

interface ICrudRepository
{
    public function findAll(array $filters): Paginator;
    public function findById($id): object | null;
    public function create(array $dto): object | null;
    // public function create(array $dto);
    public function update($id, array $dto): object | null;
    public function delete($id): object | null;
}
