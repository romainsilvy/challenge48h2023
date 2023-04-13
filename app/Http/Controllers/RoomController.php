<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomController extends Controller
{
    public function notAvailable($id)
    {
        $room = Room::find($id);

        return response()->json($room->notAvailable());
    }

    public function book($id, Request $request)
    {
        $room = Room::find($id);
        $user = auth()->user();

        $end = Carbon::parse($request->end)->addHours(2);
        $start = Carbon::parse($request->start)->addHours(2);

        $room->users()->attach($user, [
            'start_date' => $start,
            'end_date' => $end,
        ]);

        return response()->json("ok");
    }

    public function unBook($id, Request $request)
    {
        $room = Room::find($id);
        $user = auth()->user();

        $end = Carbon::parse($request->end)->addHours(2);
        $start = Carbon::parse($request->start)->addHours(2);

        $room->users()->where('user_id', $user->id)->wherePivot('start_date', $start)->wherePivot('end_date', $end)->detach();

        return response()->json("ok");
    }

    public function setUserPresent($id, $badgeId)
    {
        $room = Room::findOrFail($id);
        $user = User::where('badge', $badgeId)->first();

        $now = now();
        $room->users()->wherePivot('start_date', '<', $now)->wherePivot('end_date', '>', $now)->updateExistingPivot($user->id, ['present' => true]);

        $numUsers = $room->users()->wherePivot('start_date', '<', $now)->wherePivot('end_date', '>', $now)->count();
        $numUsersPresent = $room->users()->wherePivot('start_date', '<', $now)->wherePivot('end_date', '>', $now)->wherePivot('present', true)->count();

        return response()->json([
            'user' => $user->first_name . ' ' . $user->last_name,
            'numUsersExpected' => $numUsers,
            'numUsersPresent' => $numUsersPresent,
            'capacity' => $room->capacity,
        ]);
    }
}
