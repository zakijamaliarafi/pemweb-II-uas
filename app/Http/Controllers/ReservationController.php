<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Guest;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return view('reservations.index', [
            'reservations' => Reservation::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'guest_id' => 'required',
            'check_in' => 'required|date',
            'check_out' => 'required|date',
        ]);

        $reservation = Reservation::create([
            'room_id' => $request->room_id,
            'guest_id' => $request->guest_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
        ]);

        if ($reservation){
            return redirect()->route('reservations.index')->with('success', 'Reservation created successfully');
        }else{
            return redirect()->route('reservations.index')->with('error', 'Reservation failed to create');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'room_id' => 'required',
            'guest_id' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
        ]);

        $search = Reservation::find($request->id);
        if (!$search){
            return redirect()->route('reservations.index')->with('error', 'Reservation not found');
        }

        $store = Reservation::where('id', $request->id)->update([
            'room_id' => $request->room_id,
            'guest_id' => $request->guest_id,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
        ]);

        if ($store){
            return redirect()->route('reservations.index')->with('success', 'Reservation updated successfully');
        }else{
            return redirect()->route('reservations.index')->with('error', 'Reservation failed to update');
        }
    }

    public function getDetails(Reservation $Reservation)
    {
        $room = Room::where('id', $Reservation->room_id)->first();
        $guest = Guest::where('id', $Reservation->guest_id)->first();

        if (!$room) {
            return response()->json(['error' => 'Room not found for this reservation.']);
        }

        if (!$guest) {
            return response()->json(['error' => 'Guest not found for this reservation.']);
        }

        return response()->json([
            'reservation' => $Reservation,
            'room' => $room,
            'guest' => $guest,
        ]);
    }

    public function details()
    {
        $room = Room::get();
        $guest = Guest::get();
        return response()->json([
            'room' => $room,
            'guest' => $guest,
        ]);
    }

    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    public function destroy(Request $request)
    {
        $find = Reservation::find($request->id);
        if (!$find){
            return redirect()->route('reservations.index')->with('error', 'Reservation not found');
        }

        $destroy = Reservation::destroy($request->id);

        if ($destroy){
            return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully');
        }else{
            return redirect()->route('reservations.index')->with('error', 'Reservation failed to delete');
        }
    }
}
