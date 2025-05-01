<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::whereNotIn('name', ['admin', 'evaluator'])->get();
        return response()->json(['data' => $roles ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required',
            'label' => 'required'
        ]);

        $role = Role::create($data);
        return response()->json(['data' => $role], 201 );
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        response()->json(['data' => $role]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name'  => 'required',
            'label' => 'required'
        ]);

        $role->update($data);
        return response()->json(['data' => $role]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        $role->delete();
        response()->json([], 200);
    }
}
