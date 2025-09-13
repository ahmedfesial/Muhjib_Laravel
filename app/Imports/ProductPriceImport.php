<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductPrice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductPriceImport implements ToCollection, WithHeadingRow
{
    public $errors = [];

    public function collection(Collection $rows)
    {
        $validTypes = ['A', 'B', 'C', 'D','invalid_type'];

        foreach ($rows as $index => $row) {
            $productId = $row['product_id'] ?? null;
            $priceType = strtoupper(trim($row['price_type'] ?? ''));
            $value = $row['value'] ?? null;

            if (!$productId || !$priceType || is_null($value)) {
                $this->errors[] = [
                    'row' => $index + 2, // +2 لأنه بيبدأ من 0 والعنوان في الصف 1
                    'error' => 'Missing required fields (product_id, price_type, value)'
                ];
                continue;
            }

            if (!in_array($priceType, $validTypes)) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'product_id' => $productId,
                    'error' => "Invalid price_type: {$priceType}"
                ];
                continue;
            }

            $product = Product::find($productId);

            if (!$product) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'product_id' => $productId,
                    'error' => "Product not found"
                ];
                continue;
            }

            // Create or update price
            ProductPrice::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'price_type' => $priceType,
                ],
                [
                    'value' => $value,
                ]
            );
        }
    }
}
