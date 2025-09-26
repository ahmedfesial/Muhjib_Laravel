<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\ImportLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;

class ProductImportController extends Controller
{
public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,csv|max:10240', // 10MB Max
    ]);

    $user = Auth::user();

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $file = $request->file('file');
    $fileName = time() . '_' . $file->getClientOriginalName();

    $importLog = ImportLog::create([
        'user_id' => $user->id,
        'file_name' => $fileName,
        'status' => 'pending',
        'counts' => null,
        'errors' => null,
    ]);

    // ✅ Queue the import job
    Excel::queueImport(new ProductsImport($importLog), $file)
        ->allOnQueue('imports'); // optional: specify queue name
    // dd($importLog);
    return response()->json([
        'message' => '📥 Import started successfully.',
        'import_log_id' => $importLog->id,
        'status_url' => route('import.status', $importLog->id),
    ], 202);
}

public function status($id)
{
    $importLog = ImportLog::with('user')->findOrFail($id);

    if ($importLog->user_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    return response()->json([
        'id' => $importLog->id,
        'file_name' => $importLog->file_name,
        'status' => $importLog->status,
        'counts' => $importLog->counts,
        'errors' => $importLog->errors,
        'created_at' => $importLog->created_at->toDateTimeString(),
    ]);
}
public function export()
    {
        return Excel::download(new ProductsExport, 'products_export.xlsx');
    }
}
