<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use App\Models\ProductPrice;

class ProductPolicy
{
<<<<<<< HEAD
    public function viewAny(User $user) { return $user->can('viewAny-product'); }
    public function create(User $user) { return $user->can('create-product'); }
    public function update(User $user, ProductPrice $price) { return $user->can('update-product'); }
    public function delete(User $user, ProductPrice $price) { return $user->can('delete-product'); }
=======
    public function viewAny(User $user) { return $user->can('viewAny-product-price'); }
    public function create(User $user) { return $user->can('create-product-price'); }
    public function update(User $user, ProductPrice $price) { return $user->can('update-product-price'); }
    public function delete(User $user, ProductPrice $price) { return $user->can('delete-product-price'); }
>>>>>>> 32df490b19e8a2a1b17762bb0c6e52c36a16550e

}
