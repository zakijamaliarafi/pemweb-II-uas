<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        return view('hotels.index', [
            'hotels' => Hotel::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required',
        ]);

        $store = Hotel::create($validated);

        if ($store){
            return redirect()->route('hotels.index')->with('success', 'Hotel created successfully');
        }else{
            return redirect()->route('hotels.index')->with('error', 'Hotel failed to create');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'address' => 'required',
            'email' => 'required',
        ]);

        $search = Hotel::find($request->id);
        if (!$search){
            return redirect()->route('hotels.index')->with('error', 'Hotel not found');
        }

        $update = Hotel::where('id', $request->id)->update($validated);

        if ($update){
            return redirect()->route('hotels.index')->with('success', 'Hotel updated successfully');
        }else{
            return redirect()->route('hotels.index')->with('error', 'Hotel failed to update');
        }
    }

    public function getDetails(Hotel $hotel)
    {
        return response()->json($hotel);
    }

    public function destroy(Request $request)
    {
        $find = Hotel::find($request->id);
        if (!$find){
            return redirect()->route('hotels.index')->with('error', 'Hotel not found');
        }

        $destroy = Hotel::destroy($request->id);

        if ($destroy){
            return redirect()->route('hotels.index')->with('success', 'Hotel deleted successfully');
        }else{
            return redirect()->route('hotels.index')->with('error', 'Hotel failed to delete');
        }
    }
}
