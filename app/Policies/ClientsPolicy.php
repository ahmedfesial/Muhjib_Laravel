<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;

class ClientsPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function view(User $user, Client $client): bool
    {
        return $user->hasAnyRole(['admin', 'super_admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super_admin');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasRole('super_admin');
    }
}
