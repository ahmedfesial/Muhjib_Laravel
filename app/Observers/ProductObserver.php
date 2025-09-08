<?php

namespace App\Observers;

use App\Models\Product;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ProductObserver
{
    public function created(Product $product)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Product Created',
            'description' => "Product '{$product->name}' was created.",
        ]);
    }
}
