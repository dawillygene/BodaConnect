<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreRiderRequest;
use App\Http\Requests\Api\UpdateRiderRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;

class AdminUserController extends Controller
{
    public function __construct(private readonly UserManagementService $userManagementService) {}

    public function customers(): JsonResponse
    {
        return UserResource::collection($this->userManagementService->customers())->response();
    }

    public function riders(): JsonResponse
    {
        return UserResource::collection($this->userManagementService->riders())->response();
    }

    public function showRider(User $user): JsonResponse
    {
        abort_if($user->role !== 'rider', 404);

        return response()->json([
            'rider' => UserResource::make($user)->resolve(),
        ]);
    }

    public function storeRider(StoreRiderRequest $request): JsonResponse
    {
        $rider = $this->userManagementService->createRider($request->validated());

        return response()->json([
            'message' => "Rider {$rider->name} created.",
            'rider' => UserResource::make($rider)->resolve(),
        ], 201);
    }

    public function updateRider(UpdateRiderRequest $request, User $user): JsonResponse
    {
        $rider = $this->userManagementService->updateRider($user, $request->validated());

        return response()->json([
            'message' => "Rider {$rider->name} updated.",
            'rider' => UserResource::make($rider)->resolve(),
        ]);
    }

    public function deleteUser(User $user): JsonResponse
    {
        $updatedUser = $this->userManagementService->deactivateUser($user);

        return response()->json([
            'message' => "{$updatedUser->name} deactivated.",
            'user' => UserResource::make($updatedUser)->resolve(),
        ]);
    }
}
