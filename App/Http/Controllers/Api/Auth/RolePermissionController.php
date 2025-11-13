<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Http\Resources\Api\RoleResource;
use App\Http\Resources\Api\PermissionResource;
use App\Http\Resources\Api\UserResource;
use App\Traits\ApiResponse;
use Exception;

class RolePermissionController extends Controller
{
    use ApiResponse;

    public function roles()
    {
        try {
            $roles = Role::with('permissions')->paginate(request('per_page', 10));

            return $this->successResponse(
                RoleResource::collection($roles),
                'Roles retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to fetch roles', 500, $e->getMessage());
        }
    }

    public function createRole(RoleRequest $request)
    {
        try {
            $request->validated();

            $role = Role::create([
                'name' => $request->name,
            ]);

            return $this->successResponse(new RoleResource($role), 'Role created successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to create role', 500, $e->getMessage());
        }
    }

    public function deleteRole(Role $role)
    {
        try {
            // $role->syncPermissions([]);
            // if (method_exists($role, 'users')) {
            //     $role->users()->detach();
            // }
            \DB::table('roles')
                ->where('id', $role->id)
                ->delete();

            return $this->successResponse(null, 'Role deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete role', 500, $e->getMessage());
        }
    }

    public function permissions()
    {
        try {
            $permissions = Permission::paginate(request('per_page', 10));

            return $this->successResponse(
                PermissionResource::collection($permissions),
                'Permissions retrieved successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to fetch permissions', 500, $e->getMessage());
        }
    }

    public function createPermission(PermissionRequest $request)
    {
        try {
            $request->validated();

            $permission = Permission::create([
                'name' => $request->name,
            ]);

            return $this->successResponse(new PermissionResource($permission), 'Permission created successfully', 201);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to create permission', 500, $e->getMessage());
        }
    }

    public function deletePermission(Permission $permission)
    {
        try {
            \DB::table('permissions')
                ->where('id', $permission->id)
                ->delete();
            return $this->successResponse(null, 'Permission deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to delete permission', 500, $e->getMessage());
        }
    }

    public function assignPermissionsToRole(Request $request, Role $role)
    {
        try {
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'integer|exists:permissions,id',
            ]);

            $permissions = Permission::whereIn('id', $request->permissions)
                ->get()
                ->map(function ($permission) {
                    $permission->guard_name = 'sanctum';
                    return $permission;
                });

            $role->syncPermissions($permissions);

            return $this->successResponse(
                new RoleResource($role->load('permissions')),
                'Permissions assigned to role successfully'
            );
        } catch (Exception $e) {
            return $this->errorResponse('Failed to assign permissions to role', 500, $e->getMessage());
        }
    }

    public function assignRoleToUser(Request $request, User $user)
    {
        try {
            $request->validate([
                'roles' => 'required|array',
                'roles.*' => 'exists:roles,id'
            ]);

            $roles = Role::whereIn('id', $request->roles)
                ->get();

            if ($roles->isEmpty()) {
                return $this->errorResponse('No valid roles found', 422);
            }

            $user->syncRoles($roles);

            return $this->successResponse(new UserResource($user), 'Role assigned to user successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to assign role to user', 500, $e->getMessage());
        }
    }


    public function assignPermissionToUser(Request $request, User $user)
    {
        try {
            $request->validate([
                'permissions' => 'required|array',
                'permissions.*' => 'exists:permissions,id'
            ]);

            $permissions = Permission::whereIn('id', $request->permissions)->pluck('name')->toArray();
            $user->syncPermissions($permissions);

            return $this->successResponse(new UserResource($user), 'Permissions assigned to user successfully');
        } catch (Exception $e) {
            return $this->errorResponse('Failed to assign permissions to user', 500, $e->getMessage());
        }
    }
}