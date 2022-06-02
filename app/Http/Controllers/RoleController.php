<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Http\Requests\SaveRoleRequest;
use Illuminate\Http\Request;
use Exception;

class RoleController extends Controller
{
    /**
     * Display a listing of the Roles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response(Role::with('permission')->get(), 200);
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
            Role::create([
                'name'          => $request->name,
                'description'   => $request->description,
                'permission_id' => $permission->id
            ]);
        } catch (Exception $e) {
            $permission->forceDelete();
            return response($e->getMessage(), 500);
        }

        return $this->index();
    }

    /**
     * Display the specified resource.
     *
     * @param  roleUuid
     * @return \Illuminate\Http\Response
     */
    public function show($roleUuid)
    {
        if(Role::whereUuid($roleUuid)->exists()){
            return response(Role::whereUuid($roleUuid)->with('permission')->first(), 200);
        }
        return response()->json(['message' => 'Role Not Found'],  404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  roleUuid
     * @return \Illuminate\Http\Response
     */
    public function update(SaveRoleRequest $request, $roleUuid)
    {
        if(Role::whereUuid($roleUuid)->exists()){
            $role = Role::whereUuid($roleUuid)->with('permission')->first();
        }else{
            return response()->json(['message' => 'Role Not Found'],  404);
        }
        try{
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
     * @param  roleUuid
     * @return \Illuminate\Http\Response
     */
    public function destroy($roleUuid)
    {
        if(Role::whereUuid($roleUuid)->exists()){
            Role::whereUuid($roleUuid)->delete();
            return $this->index();
        }else{
            return response()->json(['message' => 'Role Not Found'],  404);
        }
    }
}
