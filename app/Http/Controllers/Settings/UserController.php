<?php

namespace App\Http\Controllers\Settings;

use App\Core\Settings\UserService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Common\SearchRequest;
use App\Http\Requests\Settings\ChangeUserPasswordRequest;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Http\Requests\Settings\UpdateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    public function __construct(protected UserService $userService)
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(SearchRequest $request)
    {
        return $this->success($this->userService->getUsers($request->toDTO()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        return $this->success($this->userService->createUser($request->validated()), 'User has been created.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->success($this->userService->getUser($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        return $this->success($this->userService->updateUser($user, $request->validated()), 'User updated successfully.');
    }

    /**
     * Changes the specified resource password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangeUserPasswordRequest $request, User $user)
    {
        if ($this->userService->resetPassword($user, $request->validated())) {
            return $this->success();
        }

        return $this->error('Something went wrong. Try again.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        return $this->success($this->userService->deleteUser($user), 'User deleted successfully.', 204);
    }

    /**
     * Toggle a user's active state.
     */
    public function toggle(User $user)
    {
        if ($this->userService->enableDisableUser($user)) {
            return $this->success($user->refresh(), 'User status updated.');
        }

        return $this->error('Something went wrong. Could not update user status.');
    }

    protected function resourceAbilityMap(): array
    {
        return array_merge(parent::resourceAbilityMap(), [
            'changePassword' => 'changePassword',
            'toggle' => 'edit',
        ]);
    }
}
