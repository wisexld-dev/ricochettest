<?php

namespace App\Http\Controllers;

use App\Events\MeetingReminder;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeetingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        $meetings = Meeting::where('user_id', $user->id)
            ->with('client:id,name')
            ->get();

        return response()->json($meetings);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable',
        ]);

        $validated['user_id'] = $user->id;
        $meeting = Meeting::create($validated);

        $meeting->load('client');

        event(new MeetingReminder($meeting));

        return response()->json($meeting, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Meeting $meeting)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($meeting->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($meeting->load('client:id,name'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Meeting $meeting)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($meeting->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $meeting->update($request->all());
        return response()->json($meeting);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Meeting $meeting)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($meeting->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $meeting->delete();
        return response()->json(null, 204);
    }
}
