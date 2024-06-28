<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index()
    {
        return view('guests.index', [
            'guests' => Guest::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required',
        ]);

        $store = Guest::create($validated);

        if ($store){
            return redirect()->route('guests.index')->with('success', 'Guest created successfully');
        }else{
            return redirect()->route('guests.index')->with('error', 'Guest failed to create');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required',
        ]);

        $search = Guest::find($request->id);
        if (!$search){
            return redirect()->route('guests.index')->with('error', 'Guest not found');
        }

        $update = Guest::where('id', $request->id)->update($validated);

        if ($update){
            return redirect()->route('guests.index')->with('success', 'Guest updated successfully');
        }else{
            return redirect()->route('guests.index')->with('error', 'Guest failed to update');
        }
    }

    public function getDetails(Guest $guest)
    {
        return response()->json($guest);
    }

    public function destroy(Request $request)
    {
        $find = Guest::find($request->id);
        if (!$find){
            return redirect()->route('guests.index')->with('error', 'Guest not found');
        }

        $destroy = Guest::destroy($request->id);

        if ($destroy){
            return redirect()->route('guests.index')->with('success', 'Guest deleted successfully');
        }else{
            return redirect()->route('guests.index')->with('error', 'Guest failed to delete');
        }
    }
}
