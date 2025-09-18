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
    $mostUsedProducts = DB::table('basket_products')
        ->join('products', 'products.id', '=', 'basket_products.product_id')
        ->select('products.name_en as name', DB::raw('COUNT(basket_products.product_id) as count'))
        ->groupBy('products.id', 'products.name_en')
        ->orderByDesc('count')
        ->take(5)
        ->get();

    // 2. Most Preferred Templates
    $mostPreferredTemplates = DB::table('template_products')
        ->join('templates', 'templates.id', '=', 'template_products.template_id')
        ->select('templates.name', DB::raw('COUNT(template_products.product_id) as count'))
        ->groupBy('templates.id', 'templates.name')
        ->orderByDesc('count')
        ->take(5)
        ->get();

    // 3. Most Preferred Companies (using clients.company)
    $mostPreferredCompanies = DB::table('clients')
        ->select('company', DB::raw('COUNT(*) as count'))
        ->whereNotNull('company')
        ->groupBy('company')
        ->orderByDesc('count')
        ->take(5)
        ->get();

    return response()->json([
        'mostUsedProducts' => $mostUsedProducts,
        'mostPreferredTemplates' => $mostPreferredTemplates,
        'mostPreferredCompanies' => $mostPreferredCompanies,
    ]);
}

}
