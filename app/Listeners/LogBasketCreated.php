<?php

namespace App\Listeners;

use App\Events\BasketCreated;
use App\Models\Activity;

class LogBasketCreated
{
    public function handle(BasketCreated $event)
    {
        Activity::create([
            'description' => "{$event->user->name} created basket #{$event->basket->id}"
        ]);
    }
}
