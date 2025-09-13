<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Product::select([
            'sku',
            'name_en',
            'name_ar',
            'description_ar',
            'features',
            'main_colors',
            'brand_id',
            'sub_category_id',
            'main_image',
            'pdf_hs',
            'pdf_msds',
            'pdf_technical',
            'hs_code',
            'pack_size',
            'dimensions',
            'capacity',
            'specification',
            'price',
            'is_visible',
            'quantity',
            'images',
        ])->get();
    }

    public function headings(): array
    {
        return [
            'sku',
            'name_en',
            'name_ar',
            'description_ar',
            'features',
            'main_colors',
            'brand_id',
            'sub_category_id',
            'main_image',
            'pdf_hs',
            'pdf_msds',
            'pdf_technical',
            'hs_code',
            'pack_size',
            'dimensions',
            'capacity',
            'specification',
            'price',
            'is_visible',
            'quantity',
            'images',
        ];
    }
}
