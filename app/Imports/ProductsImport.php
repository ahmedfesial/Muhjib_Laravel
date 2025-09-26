<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ImportLog;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Concerns\Importable;

class ProductsImport implements
    ToCollection,
    WithHeadingRow,
    WithChunkReading,
    ShouldQueue,
    WithEvents
{
    use Importable;

    protected $importLog;

    public function __construct(ImportLog $importLog)
    {
        $this->importLog = $importLog;
    }

    public $createdCount = 0;
    public $updatedCount = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            try {
                if (empty($row['sku'])) {
                    $this->errors[] = [
                        'row' => $index + 2,
                        'error' => 'SKU is required'
                    ];
                    continue;
                }

                $data = [
                    'name_en' => $row['name_en'] ?? null,
                    'name_ar' => $row['name_ar'] ?? null,
                    'description_ar' => $row['description_ar'] ?? null,
                    'features' => $row['features'] ?? null,
                    'main_colors' => isset($row['main_colors']) ? explode(',', $row['main_colors']) : null,
                    'brand_id' => $row['brand_id'] ?? null,
                    'sub_category_id' => $row['sub_category_id'] ?? null,
                    'main_image' => $row['main_image'] ?? null,
                    'pdf_hs' => $row['pdf_hs'] ?? null,
                    'pdf_msds' => $row['pdf_msds'] ?? null,
                    'pdf_technical' => $row['pdf_technical'] ?? null,
                    'hs_code' => $row['hs_code'] ?? null,
                    'pack_size' => $row['pack_size'] ?? null,
                    'dimensions' => $row['dimensions'] ?? null,
                    'capacity' => $row['capacity'] ?? null,
                    'specification' => $row['specification'] ?? null,
                    'price' => $row['price'] ?? 0,
                    'is_visible' => $row['is_visible'] ?? 1,
                    'quantity' => $row['quantity'] ?? 0,
                    'images' => isset($row['images']) ? explode(',', $row['images']) : null,
                    'prices' => isset($row['prices']) ? json_decode($row['prices'], true) : null,
                ];

                $product = Product::updateOrCreate(
                    ['sku' => $row['sku']],
                    $data
                );

                if ($product->wasRecentlyCreated) {
                    $this->createdCount++;
                } else {
                    $this->updatedCount++;
                }
            } catch (\Exception $e) {
                $this->errors[] = [
                    'row' => $index + 2,
                    'error' => $e->getMessage()
                ];
            }
        }
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function (AfterImport $event) {
                $this->importLog->update([
                    'status' => 'done',
                    'counts' => [
                        'created' => $this->createdCount,
                        'updated' => $this->updatedCount,
                    ],
                    'errors' => $this->errors,
                ]);
            },
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
