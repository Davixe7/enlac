<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['work_area', 'leader', 'roles'])->orderBy('name')->get();
        $data = UserResource::collection($users);
        return response()->json(compact('data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'second_last_name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'leader_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'work_area_id' => 'required|exists:work_areas,id',
            'entry_date' => 'required|date',
            'status' => 'required',
        ]);

        unset($data['role_id']);
        $data['password'] = bcrypt( $request->password );

        $user = User::create($data);

        if( $request->filled('role_id') ){
            $role = Role::find( $request->role_id );
            $user->assignRole( $role );
        }

        if( $request->boolean('is_admin') ){
            $adminRole = Role::where('name', 'like', "%admin%")->first();
            $user->assignRole( $adminRole );
        }

        if( $request->boolean('is_evaluator') ){
            $evaluatorRole = Role::where('name', 'like', "%evaluator%")->first();
            $user->assignRole( $evaluatorRole );
        }

        $user->load(['work_area', 'leader', 'roles']);
        return new UserResource( $user );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'last_name' => 'required|string',
            'second_last_name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => ['required', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'leader_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
            'work_area_id' => 'required|exists:work_areas,id',
            'entry_date' => 'required|date',
            'status' => 'required',
        ]);

        unset($data['role_id']);
        $data['password'] = $request->password ? bcrypt( $request->password ) : $user->password;

        $user->update($data);
        $user->load('roles');

        if( $request->filled('role_id') ){
            $role = Role::find( $request->role_id );
            $user->roles()->sync( $role );
        }

        if( $request->filled('is_admin') ){
            $adminRole = Role::where('name', 'like', "%admin%")->firstOrFail();
            $request->is_admin
            ? $user->assignRole( $adminRole )
            : $user->removeRole( $adminRole );
        }

        if( $request->filled('is_evaluator') ){
            $evaluatorRole = Role::where('name', 'like', "%evaluator%")->firstOrFail();
            $request->is_evaluator
            ? $user->assignRole( $evaluatorRole )
            : $user->removeRole( $evaluatorRole );
        }

        $user->load(['work_area', 'leader', 'roles']);
        return new UserResource( $user );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
