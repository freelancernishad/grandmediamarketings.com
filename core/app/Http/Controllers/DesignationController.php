<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Designation;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    // Display a listing of the resource.
    public function index()
    {
        $designations = Designation::all();
        return view('backend.designations.index', compact('designations'));
    }

    // Show the form for creating a new resource.
    public function create()
    {
        return view('backend.designations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_investment' => 'required|numeric|min:0', // Updated column name
            'bonus' => 'required|numeric|min:0', // Updated column name
            'commission_level' => 'required|numeric|min:1', // Updated column name

        ]);

        Designation::create([
            'name' => $request->name,
            'minimum_investment' => $request->minimum_investment, // Updated column name
            'bonus' => $request->bonus, // Updated column name
            'commission_level' => $request->commission_level, // Updated column name

        ]);

        return redirect()->route('admin.designations.index');
    }
    // Display the specified resource.
    public function show(Designation $designation)
    {
        return view('backend.designations.show', compact('designation'));
    }


    public function showUsers($id)
{
    // Find the designation by ID
    $designation = Designation::findOrFail($id);

    // Get users with the specified designation
    $users = User::whereHas('currentDesignation', function($query) use ($id) {
        $query->where('designation_id', $id);
    })->get();

    // Return the view with the designation and users
    return view('backend.designations.users', [
        'designation' => $designation,
        'users' => $users,
    ]);
}


    // Show the form for editing the specified resource.
    public function edit(Designation $designation)
    {
        return view('backend.designations.edit', compact('designation'));
    }

    // Update the specified resource in storage.
    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $designation->update($request->all());

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation updated successfully.');
    }

    // Remove the specified resource from storage.
    public function destroy(Designation $designation)
    {
        $designation->delete();

        return redirect()->route('admin.designations.index')
            ->with('success', 'Designation deleted successfully.');
    }
}
