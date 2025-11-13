<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\Api\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * all users
     */
    public function index(Request $request)
    {
        $filters = $request->only(['role', 'permission', 'search']);

        $users = User::filter($filters)
            ->paginate($request->get('per_page', 10));

        return $this->successResponse(
            UserResource::collection($users),
            'Users retrieved successfully'
        );
    }



    /**
     * update user
     */

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'              => 'sometimes|string|max:255',
            'email'             => 'sometimes|email|unique:users,email,' . $user->id,
            'current_password'  => 'required_with:password|string',
            'password'          => 'nullable|string|min:8|confirmed',
        ]);

        // if password is being updated, check current password
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return $this->errorResponse('Current password is incorrect', 422);
            }
            $user->password = Hash::make($request->password);
        }

        // update other fields
        if ($request->filled('name')) {
            $user->name = $request->name;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return $this->successResponse(new UserResource($user), 'User updated successfully');
    }


    /**
     * delete user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return $this->successResponse(null, 'User deleted successfully');
    }

    public function show(User $user)
    {
        try {
            return $this->successResponse(new UserResource($user), 'User retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to load user relations', 500, $e->getMessage());
        }
    }
}