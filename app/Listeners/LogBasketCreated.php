<?php

namespace App\Listeners;

use App\Events\BasketCreated;
use App\Models\Activity;

class LogBasketCreated
{
    public function handle(BasketCreated $event)
    {
        Activity::create([
            'description' => "{$event->user->name} created basket #{$event->basket->id}",
            'event_type' => 'basket_created',
            'user_id' => $event->user->id, // لو عايز تربط الحدث بالمستخدم
        ]);
    }
}
