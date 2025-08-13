<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductPrice;

class ProductPolicy
{
    public function viewAny(User $user) { return $user->can('viewAny-product-price'); }
    public function create(User $user) { return $user->can('create-product-price'); }
    public function update(User $user, ProductPrice $price) { return $user->can('update-product-price'); }
    public function delete(User $user, ProductPrice $price) { return $user->can('delete-product-price'); }

}
