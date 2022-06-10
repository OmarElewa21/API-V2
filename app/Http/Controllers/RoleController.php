<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SaveRoleRequest;
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
    public function index()
    {
        return response(Role::get(), 200);
    }


    /**
     * Store a newly created Role in database.
     *
     * @param  App\Models\Requests\SaveRoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SaveRoleRequest $request)
    {
        $permission = new Permission;
        $permission->fill([
            'permissions_set'   =>  $request->permission_set
        ])->save();

        try {
            $role = New Role;
            $role->fill([
                'name'          => Str::lower($request->name),
                'description'   => $request->description,
                'permission_id' => $permission->id
            ])->save();
        } catch (Exception $e) {
            $permission->forceDelete();
            return response($e->getMessage(), 500);
        }

        return response($role, 200);
    }


    /**
     * Display the specified resource.
     *
     *@param  \App\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return response($role, 200);
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
                'permission_id' => $role->permission->id
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
            return $this->index();
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
        return $this->index();
    }
}
