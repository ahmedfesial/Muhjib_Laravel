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
use App\Models\Product;


class PriceUploadLogController extends Controller
{
    use AuthorizesRequests;
    public function index()
    {

        $logs = PriceUploadLog::with('user')->latest()->get();
        $data =PriceUploadLogResource::collection($logs);
        return response()->json(['message'=>'Prices Logs Retrieved Successfully', 'data' => $data],200);
    }

  public function store(StorePriceUploadLogRequest $request)
{
    $items = $request->input('items');
    $updatedItems = [];

DB::transaction(function () use ($items, &$updatedCount, &$updatedItems) {
    foreach ($items as $item) {
        $product = Product::find($item['product_id']);

        if ($product) {
            ProductPrice::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'price_type' => strtoupper($item['price_type']),
                ],
                [
                    'value' => $item['value'],
                ]
            );

            $updatedItems[] = [
                'product_id' => $product->id,
                'product_name' => $product->name_en,
                'price_type' => strtoupper($item['price_type']),
                'value' => $item['value']
            ];

            $updatedCount++;
        }
    }
});

$log = PriceUploadLog::create([
    'uploaded_by' => Auth::id(),
    'file_name' => 'manual_input_' . now()->timestamp,
    'products_updated' => $updatedCount,
]);

$user = Auth::user();

return response()->json([
    'message' => 'Prices updated and logged successfully.',
    'log' => [
        'id' => $log->id,
        'file_name' => $log->file_name,
        'uploaded_by' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email
        ],
        'products_updated' => $log->products_updated,
        'created_at' => $log->created_at->toDateTimeString(),
    ],
    'updated_prices' => $updatedItems
], 201);
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
