<?php

namespace App\Http\Controllers;

use App\Models\Legend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LegendController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Legends Retrived Successfully','data' => Legend::all()],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('legends', 'public');

        $legend = Legend::create([
            'name' => $request->name,
            'image' => $path,
        ]);

        return response()->json(['message' => 'Legend created Successfully', 'data' => $legend],201);
    }

    public function destroy(Legend $legend)
    {
        Storage::disk('public')->delete($legend->image);
        $legend->delete();

        return response()->json(['message' => 'Legend deleted Successfully'],200);
    }
}
