<?php

namespace App\Http\Controllers;

use App\Models\PriceUploadLog;
use App\Models\ProductPrice;
use App\Http\Requests\StorePriceUploadLogRequest;
use App\Http\Resources\PriceUploadLogResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PriceUploadLogController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {
        // $this->authorize('viewAny', PriceUploadLog::class);

        $logs = PriceUploadLog::with('user')->latest()->get();
        $data =PriceUploadLogResource::collection($logs);
        return response()->json(['message'=>'Prices Logs Retrieved Successfully', 'data' => $data],200);
    }

    public function store(StorePriceUploadLogRequest $request)
    {
        // $this->authorize('create', PriceUploadLog::class);

        $file = $request->file('file_name');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('price_uploads', $fileName, 'public');

        $updatedCount = 0;

        DB::transaction(function () use ($file, &$updatedCount) {
            $collection = Excel::toCollection(null, $file)[0];

            foreach ($collection as $row) {
                // Assuming Excel columns: product_code | price_type | value
                $product = \App\Models\Product::first();
                if ($product) {
                    ProductPrice::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'price_type' => $row['price_type']
                        ],
                        ['value' => $row['value']]
                    );
                    $updatedCount++;
                }
            }
        });
        $logs = PriceUploadLog::create([
            'uploaded_by' => Auth::id(),
            'file_name' => $fileName,
            'products_updated' => $updatedCount,
        ]);
        $data=new PriceUploadLogResource($logs);
        return response()->json(['message'=>'Prices Logs Created Successfully', 'data' => $data],201);
    }

    public function show(PriceUploadLog $priceUploadLog)
    {
        $this->authorize('view', $priceUploadLog);
        $priceUploadLog = PriceUploadLog::findOrFail($priceUploadLog->id);
        if(!$priceUploadLog){
            return response()->json([
                'message' => 'price Upload Log not found.',
            ], 404);
        }
        $data =new PriceUploadLogResource($priceUploadLog->load('user'));
        return response()->json(['message'=>'Prices Logs Retrieved Successfully', 'data' => $data],200);
    }
}
