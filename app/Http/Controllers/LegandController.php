<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\legand;

class LegandController extends Controller
{
    public function index()
    {
        $legands = legand::all();

        return response()->json([
            'data' => $legands
        ]);
    }
     public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('image')->store('legands', 'public');

        $legand = legand::create([
            'image' => $path
        ]);

        return response()->json([
            'message' => 'Legand created successfully',
            'data' => $legand
        ]);
    }

    
}
