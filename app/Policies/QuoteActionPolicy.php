<?php

namespace App\Policies;

use App\Models\User;
use App\Models\QuoteAction;

class QuoteActionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_quote_actions');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_quote_actions');
    }
}
