<?php

namespace App\Interfaces\Auth;

use Illuminate\Contracts\Pagination\Paginator;

interface IAuthRepository
{
    public function getAccount($id): object | null;
    public function register(array $dto): array;
    public function login(array $dto): array;
    public function logout(): array;
    public function updateAccount(array $dto): array;
    public function updatePassword(array $dto): array;
    public function removeAccount(): array;

    public function fetchAccounts(array $filterData): Paginator;
    public function fetchSingleAccount($id): object | null;
    public function editPassword($id, array $dto): object | null;
    public function editRole($id, array $dto): object | null;
}
