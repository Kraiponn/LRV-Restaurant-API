<?php

namespace App\Repositories;

use App\Interfaces\Auth\IAuthRepository;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\PersonalAccessToken;

class AuthRepository implements IAuthRepository
{
    /*
    |------------------------------------------------------
    |   Get account
    |   @Param      int
    |   @Return     User | null
    |------------------------------------------------------
    */
    public function getAccount($id): ?User
    {
        $user = User::find($id);

        if (!$user) {
            throw new Exception('User not found', 404);
        }

        return $user;
    }

    /*
    |------------------------------------------------------
    |   Register new member
    |   @param      array(RegisterRequest)
    |   @return     array
    |------------------------------------------------------
    */
    public function register(array $dto): array
    {
        $registerFields = $this->fieldsToRegister($dto);
        $result = User::create($registerFields);

        if (!$result) {
            throw new Exception('Sorry, something went wrong. Please try again.', 500);
        }

        $user = User::find($result['id']);
        $response = $this->getAuthResponse($user, $dto['device_name']);

        DB::commit();
        return $response;
    }

    /*
    |------------------------------------------------------
    |   Login to access application
    |   @param      array(LoginRequest)
    |   @return     array
    |------------------------------------------------------
    */
    public function login(array $dto): array
    {
        if (!Auth::attempt(['email' => $dto['email'], 'password' => $dto['password']])) {
            throw new Exception('Invalid email or password', 400);
        }

        $user = Auth::user();
        $response = $this->getAuthResponse($user, $dto['device_name']);

        return $response;
    }

    /*
    |------------------------------------------------------
    |   Logout from application
    |------------------------------------------------------
    */
    public function logout(): array
    {
        $user = Auth::user();
        $foundUser = User::find($user->id);

        if (!empty($foundUser->tokens())) {
            $foundUser->tokens()->delete();
        }

        return [];
    }

    /*
    |------------------------------------------------------
    |   Update password
    |   @param      array   $dto
    |   return      array
    |------------------------------------------------------
    */
    public function updatePassword(array $dto): array
    {
        $user = User::find(Auth::user()->id);

        if (!Hash::check($dto['current_password'], $user->password)) {
            throw new Exception('Password is incorrect.', 400);
        }

        $user->password = Hash::make($dto['new_password']);
        $user->save();

        return ['result' => $user];
    }

    /*
    |------------------------------------------------------
    |   Update an account
    |
    |   @param      array   $dto
    |   @param      int     $id
    |   return      array
    |------------------------------------------------------
    */
    public function updateAccount(array $dto): array
    {
        $id = Auth::user()->id;
        $user = User::find($id);

        if (!$user) {
            DB::rollBack();
            throw new Exception('Account not found', 404);
        }

        $user->name = $dto['name'] ?? $user->name;
        $user->email = $dto['email'] ?? $user->email;

        if ($dto['image'] != null) {
            if ($user->image != 'nopic.png') {
                Storage::disk('public')->delete('upload/accounts/' . $user->image);
            }

            $fileExtension = $dto['image']->getClientOriginalExtension();
            $fileName = uniqid() . '.' . $fileExtension;

            $user->image = $fileName;
            Storage::disk('public')->put(
                'upload/accounts/' . $fileName,
                file_get_contents($dto['image'])
            );
        }

        $user->save();

        return ['id' => $id, 'result' => $user];
    }

    /*
    |------------------------------------------------------
    |   Remove an account
    |   return      array
    |------------------------------------------------------
    */
    public function removeAccount(): array
    {
        $id = Auth::user()->id;

        $user = User::where('id', $id)->first();
        if (!$user) {
            throw new Exception('User not found', 404);
        }

        // Logged out from db before remove account
        if (!empty($user->tokens())) {
            $user->tokens()->delete();
        }

        if ($user->image != 'nopic.png') {
            Storage::disk('public')->delete('upload/accounts/' . $user->image);
        }

        $user->delete();

        DB::commit();
        return ['result' => $user];
    }

    /*
    |------------------------------------------------------
    |   Fetch accounts with pagination
    |
    |   @Query      searchKey(name field)
    |   @Query      page
    |   @Query      pageSize
    |   @Query      orderBy
    |   @Query      order(asc, desc)
    |
    |   @Return      array
    |------------------------------------------------------
    */
    public function fetchAccounts(array $filterData): Paginator
    {
        $filter = $this->getFilterData($filterData);

        $query = User::orderBy($filter['orderBy'], $filter['order']);

        if (!empty($filter['searchKey'])) {
            $query = $query->where(function ($query) use ($filter) {
                $searched = '%' . $filter['searchKey'] . '%';

                $query->where('name', 'like', $searched);
            });
        }

        return $query->paginate($filter['pageSize']);
    }

    /*
    |------------------------------------------------------
    |   Fetch single account
    |   @Param      int
    |   @Return     User | null
    |------------------------------------------------------
    */
    public function fetchSingleAccount($id): ?User
    {
        $user = User::find($id);

        if ($user == null) {
            throw new Exception('Account not found', 404);
        }

        return $user;
    }

    /*
    |------------------------------------------------------
    |   Update password of a member
    |   @Param      int
    |   @Param      array
    |   return      array
    |------------------------------------------------------
    */
    public function editPassword($id, array $dto): ?User
    {
        $user = User::find($id);

        if ($user == null) {
            throw new Exception('Account not found', 404);
        }

        $user->password = Hash::make($dto['password']);
        $user->save();

        DB::commit();
        return $user;
    }

    /*
    |------------------------------------------------------
    |   Update role of member
    |   @Param      int
    |   @Param      array
    |   return      array
    |------------------------------------------------------
    */
    public function editRole($id, array $dto): ?User
    {
        $user = User::find($id);

        if ($user == null) {
            throw new Exception('Account not found', 404);
        }

        $user->role_type = $dto['role_type'];
        $user->save();

        DB::commit();
        return $user;
    }

    //##################################################################################
    //                              Helper Functions
    //##################################################################################
    /**
     * ----------------------------------------------
     *  Generate fields for register the new member
     * ----------------------------------------------
     */
    private function fieldsToRegister(array $data): array
    {
        return [
            'name' => $data['name'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ];
    }

    /**
     * ----------------------------------------
     *  Generate filter data
     * ----------------------------------------
     */
    private function getFilterData(array $filters): array
    {
        $defaultValues = [
            'orderBy' => 'id',
            'order' => 'desc',
            'page' => 1,
            'pageSize' => 10,
            'searchKey' => ''
        ];

        return array_merge($defaultValues, $filters);
    }

    /**
     * ----------------------------------------
     *  Generate auth response
     * ----------------------------------------
     */
    private function getAuthResponse(User $user, string $deviceName): array
    {
        if (!empty($user->tokens())) {
            $user->tokens()->delete();
        }

        $expiresTime = Carbon::now()->addMinutes(config('sanctum.expiration'));
        $tokenInstance = $user->createToken($deviceName, [$user->role_type], $expiresTime);

        $personalAccessToken = PersonalAccessToken::findToken($tokenInstance->plainTextToken);

        // $tokenExpiresIn = $personalAccessToken
        //     ->created_at
        //     ->addMinutes(config('sacntum.expiration'));
        $tokenExpiresAt = $personalAccessToken->expires_at;

        return [
            'user' => $user,
            'access_token' => $tokenInstance->plainTextToken,
            'token_type' => 'Bearer',
            'token_expires_at' => $expiresTime,
            'token_expires_at2' => Carbon::parse($tokenExpiresAt)->toDateTimeLocalString(),
        ];
    }
}
