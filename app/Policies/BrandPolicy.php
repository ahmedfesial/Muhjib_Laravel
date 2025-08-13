<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Brand;

class BrandPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, Brand $brand): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, Brand $brand): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, Brand $brand): bool
    {
        return $user->hasRole('super_admin');
    }
}


