<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Designation;
use App\Models\UserDesignation;
use Illuminate\Http\Request;

class UserDesignationController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $userDesignations = UserDesignation::with('user', 'designation')->get();
        return view('backend.user-designations.index', compact('userDesignations'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        $users = User::all();
        $designations = Designation::all();
        return view('backend.user-designations.create', compact('users', 'designations'));
    }

    // Store a newly created resource in storage.
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        UserDesignation::create($request->all());

        return redirect()->route('user-designations.index')
            ->with('success', 'User designation created successfully.');
    }

    // Display the specified resource.
    public function show(UserDesignation $userDesignation)
    {
        return view('backend.user-designations.show', compact('userDesignation'));
    }

    // Show the form for editing the specified resource.
    public function edit(UserDesignation $userDesignation)
    {
        $users = User::all();
        $designations = Designation::all();
        return view('backend.user-designations.edit', compact('userDesignation', 'users', 'designations'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, UserDesignation $userDesignation)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $userDesignation->update($request->all());

        return redirect()->route('user-designations.index')
            ->with('success', 'User designation updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(UserDesignation $userDesignation)
    {
        $userDesignation->delete();

        return redirect()->route('user-designations.index')
            ->with('success', 'User designation deleted successfully.');
    }
}
