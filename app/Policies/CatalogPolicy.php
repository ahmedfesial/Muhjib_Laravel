<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Catalog;

class CatalogPolicy
{
    public function viewAny(User $user)
{
    return $user->isAdmin() || $user->isSuperAdmin();
}

public function view(User $user, Catalog $catalog)
{
    return $user->isAdmin() || $user->isSuperAdmin() || $user->id === $catalog->basket->created_by;
}

public function create(User $user)
{
    return $user->isAdmin() || $user->isSuperAdmin();
}
}
