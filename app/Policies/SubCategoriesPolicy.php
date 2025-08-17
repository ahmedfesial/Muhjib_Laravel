<?php

namespace App\Policies;

use App\Models\SubCategories;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubCategoriesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, SubCategories $subCategory): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, SubCategories $subCategory): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, SubCategories $subCategory): bool
    {
        return $user->hasRole('super_admin');
    }
}
