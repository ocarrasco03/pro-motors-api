<?php

namespace App\Http\Controllers\Api\V1\Settings;

use App\Application\DTOs\Common\SearchDTO;
use App\Application\DTOs\User\StoreUserDTO;
use App\Application\DTOs\User\UpdateUserDTO;
use App\Application\Services\User\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Http\Requests\Settings\UpdateUserRequest;
use App\Http\Resources\Settings\UserCollection;
use App\Http\Resources\Settings\UserResource;
use App\Models\User;

class UserController extends Controller
{
    protected $dto;

    public function __construct(protected UserService $userService)
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request)
    {
        $this->dto = SearchDTO::fromArray($request->validated());
        $users = $this->userService->getAll($this->dto);
        return $this->success(new UserCollection($users));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $this->dto = StoreUserDTO::fromArray($request->validated());
        $user = $this->userService->create($this->dto);

        return $this->success(new UserResource($user), 'User has been created.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $company = $this->userService->getUser($user);

        abort_if(!$company, 404, 'User not found.');

        return $this->success(new UserResource($company));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $this->dto = UpdateUserDTO::fromArray($request->validated());
        $result = $this->userService->update($user, $this->dto);

        return $this->success(new UserResource($result), 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return $this->success($this->userService->delete($user), 'User deleted successfully.', 204);
    }

    /**
     * Toggle a user's active state.
     */
    public function toggle(User $user)
    {
        // if ($this->userService->enableDisableUser($user)) {
        //     return $this->success($user->refresh(), 'User status updated.');
        // }

        return $this->error('Something went wrong. Could not update user status.');
    }

    protected function resourceAbilityMap(): array
    {
        return array_merge(parent::resourceAbilityMap(), [
            'toggle' => 'edit',
        ]);
    }
}
