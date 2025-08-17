<?php

namespace App\Policies;

use App\Models\Basket;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BasketsPolicy
{
    public function viewAny(User $user)
{
    return $user->hasRole(['admin', 'super_admin']);
}

public function view(User $user, Basket $basket)
{
    return $user->id === $basket->created_by || $user->hasRole(['admin', 'super_admin']);
}

public function create(User $user)
{
    return $user->hasRole(['admin', 'super_admin']);
}

public function update(User $user, Basket $basket)
{
    return $user->id === $basket->created_by || $user->hasRole(['admin', 'super_admin']);
}

public function delete(User $user, Basket $basket)
{
    return $user->hasRole(['admin', 'super_admin']);
}

}
