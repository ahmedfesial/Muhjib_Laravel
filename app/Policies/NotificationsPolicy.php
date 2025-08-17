<?php
namespace App\Policies;

use App\Models\User;
use App\Models\Notification;

class NotificationsPolicy
{
    public function viewAny(User $user)
{
    return true;
}

public function create(User $user)
{
    return $user->hasRole(['admin', 'super_admin', 'user']);
}

public function update(User $user, Notification $notification)
{
    return $user->id === $notification->receiver_id;
}
}
