<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Basket;

class BasketCreated
{
    use Dispatchable, SerializesModels;

    public $user;
    public $basket;

    public function __construct(User $user, Basket $basket)
    {
        $this->user = $user;
        $this->basket = $basket;
    }
}
