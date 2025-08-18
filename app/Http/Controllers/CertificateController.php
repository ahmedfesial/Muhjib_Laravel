<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Certificate;
class CertificateController extends Controller
{
    public function index()
    {
        $certificate = Certificate::all();

        return response()->json([
            'data' => $certificate
        ]);
    }
     public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $path = $request->file('image')->store('certificates', 'public');

        $certificate = Certificate::create([
            'image' => $path
        ]);

        return response()->json([
            'message' => 'Certificate created successfully',
            'data' => $certificate
        ]);
    }

    
}
