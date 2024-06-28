<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index()
    {
        return view('rooms.index', [
            'rooms' => Room::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required',
            'number' => 'required',
            'type' => 'required',
            'price' => 'required',
        ]);

        $room = Room::create([
            'hotel_id' => $request->hotel_id,
            'number' => $request->number,
            'type' => $request->type,
            'price' => $request->price,
        ]);

        if ($room){
            return redirect()->route('rooms.index')->with('success', 'Room created successfully');
        }else{
            return redirect()->route('rooms.index')->with('error', 'Room failed to create');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required',
            'number' => 'required',
            'type' => 'required',
            'price' => 'required',
        ]);

        $search = Room::find($request->id);
        if (!$search){
            return redirect()->route('rooms.index')->with('error', 'Room not found');
        }

        $store = Room::where('id', $request->id)->update([
            'hotel_id' => $request->hotel_id,
            'number' => $request->number,
            'type' => $request->type,
            'price' => $request->price,
        ]);

        if ($store){
            return redirect()->route('rooms.index')->with('success', 'Room updated successfully');
        }else{
            return redirect()->route('rooms.index')->with('error', 'Room failed to update');
        }
    }

    public function getDetails(Room $Room)
    {
        $hotel = Hotel::where('id', $Room->hotel_id)->first();

        if (!$hotel) {
            return response()->json(['error' => 'Hotel not found for this room.']);
        }

        return response()->json([
            'room' => $Room,
            'hotel' => $hotel
        ]);
    }

    public function details()
    {
        $hotel = Hotel::get();
        return response()->json([
            'hotel' => $hotel
        ]);
    }

    public function edit($id)
    {
        $room = Room::findOrFail($id);
        return response()->json($room);
    }

    public function destroy(Request $request)
    {
        $find = Room::find($request->id);
        if (!$find){
            return redirect()->route('rooms.index')->with('error', 'Room not found');
        }

        $destroy = Room::destroy($request->id);

        if ($destroy){
            return redirect()->route('rooms.index')->with('success', 'Room deleted successfully');
        }else{
            return redirect()->route('rooms.index')->with('error', 'Room failed to delete');
        }
    }
}
