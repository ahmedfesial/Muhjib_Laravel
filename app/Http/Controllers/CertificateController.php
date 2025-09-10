<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CertificateController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Certificates Retrived Successfully','data' => Certificate::all()],200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|max:2048',
        ]);

        $path = $request->file('image')->store('certificates', 'public');

        $certificate = Certificate::create([
            'name' => $request->name,
            'image' => $path,
        ]);

        return response()->json(['message' => 'Certificate Created Successfully', 'data' => $certificate],201);
    }

    public function destroy(Certificate $certificate)
    {
        Storage::disk('public')->delete($certificate->image);
        $certificate->delete();

        return response()->json(['message' => 'Certificate deleted Successfully'],200);
    }
}
