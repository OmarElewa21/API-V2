<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserPermission;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SaveRoleRequest;
use App\Http\Requests\ChangeUserPermissionRequest;
use Illuminate\Http\Request;
use Exception;

use Illuminate\Support\Str;

class RoleController extends Controller
{
    /**
     * Display a listing of the Roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Role::with('users:id,role_id,name')->withCount('users');

        Role::applyFilter($request, $data);

        $filterOptions = Role::getFilterForFrontEnd($data);

        return response(
            $filterOptions->merge(
                collect(
                    $data->paginate(is_numeric($request->paginationNumber) ? $request->paginationNumber : 5)
                )->forget(['links', 'first_page_url', 'last_page_url', 'next_page_url', 'path', 'prev_page_url']))
            ,200);
    }


    /**
     * Store a newly created Role in database.
     *
     * @param  App\Models\Requests\SaveRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveRoleRequest $request)
    {
        if(Role::where('name', $request->name)->exists()){
            return response()->json(['errors' => 'role already exists'], 409);
        }
        $permission = new Permission;
        $permission->fill([
            'permissions_set'   =>  $request->permission_set
        ])->save();

        try {
            $role = New Role;
            $role->fill([
                'name'          => Str::lower($request->name),
                'description'   => $request->description,
                'permission_id' => $permission->id,
                'created_by'    => auth()->id()
            ])->save();
        } catch (Exception $e) {
            $permission->forceDelete();
            return response($e->getMessage(), 500);
        }

        return $this->show($role);
    }


    /**
     * Display the specified resource.
     *
     *@param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return $role->load('users:id,role_id,name')->loadCount('users')->load('permission');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(SaveRoleRequest $request, Role $role)
    {
        try{
            if($role->is_fixed){
                return response()->json(['message' => 'Role is fixed and cannot be updated'], 403);
            }
            $role->update([
                'name'          => $request->name,
                'description'   => $request->description,
                'permission_id' => $role->permission->id,
                'updated_by'    => auth()->id()
            ]);
            $role->permission->update([
                'permissions_set'   =>  $request->permission_set
            ]);

        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
        return response()->json($role, 200);
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        try {
            if($role->is_fixed){
                return response()->json(['message' => 'Role is fixed and cannot be deleted'], 403);
            }
            $role->delete();
            return $this->index(new Request);
        } catch (Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    /**
     * Remove multiple roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function massDelete(Request $request)
    {
        DB::beginTransaction();
        try {
            foreach($request->all() as $role_uuid){
                if(Str::isUuid($role_uuid) && Role::whereUuid($role_uuid)->exists()){
                    $role = Role::whereUuid($role_uuid)->firstOrFail();
                    if($role->is_fixed){
                        throw new Exception("Forbidden to delete a fixed role");
                    }
                    $role->delete();
                }else{
                    throw new Exception("data is not valid");
                }
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $this->index(new Request);
    }

    /**
     * @param App\Models\User
     */
    public function changeUserPermission(User $user, ChangeUserPermissionRequest $request)
    {
        DB::beginTransaction();
        try {
            if($user->permission_by_role){
                $user->update([
                    'permission_by_role'    => false,
                    'updated_by'            => auth()->id()
                ]);
            }
            $permission = Permission::create([
                'permissions_set'   =>  $request->all()
            ]);
            if(UserPermission::where('user_id', $user->id)->exists()){
                UserPermission::where('user_id', $user->id)->update([
                    'permission_id'     => $permission->id
                ]);
            }else{
                UserPermission::create([
                    'user_id'           => $user->id,
                    'permission_id'     => $permission->id
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response($e->getMessage(), 500);
        }
        DB::commit();
        return $user->append('permission_set');
    }
}
