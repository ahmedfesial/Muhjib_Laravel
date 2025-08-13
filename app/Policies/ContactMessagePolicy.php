<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ContactMessage;

class ContactMessagePolicy
{
    public function viewAny(User $user)
{
    return in_array($user->role, ['admin', 'super_admin']);
}

public function view(User $user, ContactMessage $contactMessage)
{
    return in_array($user->role, ['admin', 'super_admin']);
}

public function update(User $user, ContactMessage $contactMessage)
{
    return in_array($user->role, ['admin', 'super_admin']);
}

public function delete(User $user, ContactMessage $contactMessage)
{
    return in_array($user->role, ['admin', 'super_admin']);
}

    public function create(User $user): bool
    {
        // allow anyone to submit
        return true;
    }
}
