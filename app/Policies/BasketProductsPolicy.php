<?php

namespace App\Policies;

use App\Models\BasketProduct;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BasketProductsPolicy
{
    public function create(User $user)
{
    return $user->hasRole(['admin', 'super_admin']);
}

public function update(User $user, BasketProduct $basketProduct)
{
    return $user->hasRole(['admin', 'super_admin']) ||
           $user->id === $basketProduct->basket->created_by;
}

public function delete(User $user, BasketProduct $basketProduct)
{
    return $user->hasRole(['admin', 'super_admin']) ||
           $user->id === $basketProduct->basket->created_by;
}
}
