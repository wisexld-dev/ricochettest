<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json(Client::where('user_id', $user->id)->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:clients',
            'phone' => 'nullable',
            'company' => 'nullable',
        ]);

        $client = $user->clients()->create($validated);

        return response()->json($client, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($client->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($client->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $client->update($request->all());
        return response()->json($client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $user = JWTAuth::parseToken()->authenticate();

        if ($client->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $client->delete();
        return response()->json(null, 204);
    }
}
