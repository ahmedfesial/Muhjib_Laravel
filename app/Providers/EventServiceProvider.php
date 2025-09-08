<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\BasketCreated::class => [
            \App\Listeners\LogBasketCreated::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
