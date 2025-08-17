<?php

namespace App\Policies;
use App\Models\User;
use App\Models\PriceUploadLog;

class PriceUploadLogPolicy
{
    public function viewAny(User $user)
{
    return $user->hasRole('super_admin');
}

public function view(User $user, PriceUploadLog $log)
{
    return $user->hasRole('super_admin');
}

public function create(User $user)
{
    return $user->hasRole('super_admin');
}
}
