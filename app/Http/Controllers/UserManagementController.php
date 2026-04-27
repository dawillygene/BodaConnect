<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function __construct(private readonly UserManagementService $userManagementService) {}

    public function customers(Request $request): JsonResponse|View
    {
        $customers = $this->userManagementService->customers();

        if ($request->expectsJson()) {
            return response()->json($customers);
        }

        return view('admin.customers.index', ['customers' => $customers]);
    }

    public function riders(Request $request): JsonResponse|View
    {
        $riders = $this->userManagementService->riders();

        if ($request->expectsJson()) {
            return response()->json($riders);
        }

        return view('admin.riders.index', ['riders' => $riders]);
    }

    public function createRider(Request $request): JsonResponse|View
    {
        if ($request->expectsJson()) {
            return response()->json([
                'fields' => ['name', 'email', 'phone', 'password', 'password_confirmation', 'status'],
                'default_status' => 'active',
            ]);
        }

        return view('admin.riders.create');
    }

    public function storeRider(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'string', 'confirmed', 'min:8'],
            'status' => ['nullable', 'in:active,inactive'],
        ]);

        $rider = $this->userManagementService->createRider($validated);

        if ($request->input('_response') === 'web') {
            return redirect()->route('admin.riders.index')->with('success', "Rider {$rider->name} created.");
        }

        return response()->json($rider, 201);
    }

    public function editRider(Request $request, User $user): JsonResponse|View
    {
        abort_if($user->role !== 'rider', 404);

        if ($request->expectsJson()) {
            return response()->json($user);
        }

        return view('admin.riders.edit', ['rider' => $user]);
    }

    public function updateRider(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone,'.$user->id],
            'password' => ['nullable', 'string', 'confirmed', 'min:8'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        $rider = $this->userManagementService->updateRider($user, $validated);

        if ($request->input('_response') === 'web') {
            return redirect()->route('admin.riders.index')->with('success', "Rider {$rider->name} updated.");
        }

        return response()->json($rider);
    }

    public function deleteUser(Request $request, User $user): JsonResponse|RedirectResponse
    {
        $updatedUser = $this->userManagementService->deactivateUser($user);

        if ($request->input('_response') === 'web') {
            return redirect()->back()->with('success', "{$updatedUser->name} deactivated.");
        }

        return response()->json($updatedUser);
    }
}
