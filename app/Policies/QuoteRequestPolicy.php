<?php

namespace App\Policies;

use App\Models\User;
use App\Models\QuoteRequest;

class QuoteRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('view_quote_requests');
    }

    public function view(User $user, QuoteRequest $quoteRequest): bool
    {
        return $user->hasPermission('view_quote_requests');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('create_quote_requests');
    }

    public function update(User $user, QuoteRequest $quoteRequest): bool
    {
        return $user->hasPermission('update_quote_requests');
    }
}
