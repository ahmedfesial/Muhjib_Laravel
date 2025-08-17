<?php

namespace App\Policies;

use App\Models\User;
use App\Models\MainCategories;

class MainCategoriesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, MainCategories $mainCategory): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, MainCategories $mainCategory): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, MainCategories $mainCategory): bool
    {
        return $user->hasRole('super_admin');
    }
}
