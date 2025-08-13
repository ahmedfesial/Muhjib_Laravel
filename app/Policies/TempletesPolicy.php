<?php

namespace App\Policies;

use App\Models\Template;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class TempletesPolicy
{
   use HandlesAuthorization;

    public function viewAny(User $user): bool { return $user->hasRole(['admin', 'super_admin']); }
    public function view(User $user, Template $template): bool { return $user->hasRole(['admin', 'super_admin']); }
    public function create(User $user): bool { return $user->hasRole(['super_admin']); }
    public function delete(User $user, Template $template): bool { return $user->hasRole(['super_admin']); }
}
