<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\Catalog;

class AnalyticsController extends Controller
{
    // AnalyticsController.php

public function index()
{
    // 1. Most Used Products
    $totalUsedProducts = DB::table('basket_products')->count();

    $mostUsedProducts = DB::table('basket_products')
        ->join('products', 'products.id', '=', 'basket_products.product_id')
        ->select('products.name_en as name', DB::raw('COUNT(basket_products.product_id) as count'))
        ->groupBy('products.id', 'products.name_en')
        ->orderByDesc('count')
        ->take(5)
        ->get()
        ->map(function ($item) use ($totalUsedProducts) {
            $item->percentage = $totalUsedProducts > 0
                ? round(($item->count / $totalUsedProducts) * 100, 2)
                : 0;
            unset($item->count); // Remove raw count if you only want percentage
            return $item;
        });

    // 2. Most Preferred Templates
    $totalTemplateUsages = DB::table('template_products')->count();

    $mostPreferredTemplates = DB::table('template_products')
        ->join('templates', 'templates.id', '=', 'template_products.template_id')
        ->select('templates.name', DB::raw('COUNT(template_products.product_id) as count'))
        ->groupBy('templates.id', 'templates.name')
        ->orderByDesc('count')
        ->take(5)
        ->get()
        ->map(function ($item) use ($totalTemplateUsages) {
            $item->percentage = $totalTemplateUsages > 0
                ? round(($item->count / $totalTemplateUsages) * 100, 2)
                : 0;
            unset($item->count);
            return $item;
        });

    // 3. Most Preferred Companies
    $totalCompanies = DB::table('clients')
        ->whereNotNull('company')
        ->count();

    $mostPreferredCompanies = DB::table('clients')
    ->select('name', DB::raw('COUNT(*) as count'))
    ->whereNotNull('company')
    ->groupBy('name')
    ->orderByDesc('count')
    ->take(5)
    ->get()
    ->map(function ($item) use ($totalCompanies) {
        $item->percentage = $totalCompanies > 0
            ? round(($item->count / $totalCompanies) * 100, 2)
            : 0;
        unset($item->count);
        return $item;
    });


    return response()->json([
        'mostUsedProducts' => $mostUsedProducts,
        'mostPreferredTemplates' => $mostPreferredTemplates,
        'mostPreferredCompanies' => $mostPreferredCompanies,
    ]);
}


}
