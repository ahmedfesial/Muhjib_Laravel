<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductPriceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::with('prices')->get()->map(function ($product) {
            $prices = $product->prices->keyBy('price_type');
            return [
                'product_id' => $product->id,
                'product_name' => $product->name_en,
                'sku' => $product->sku,
                'A' => $prices['A']->value ?? '',
                'B' => $prices['B']->value ?? '',
                'C' => $prices['C']->value ?? '',
                'D' => $prices['D']->value ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'SKU',
            'A',
            'B',
            'C',
            'D',
        ];
    }
}
