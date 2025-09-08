<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ClientObserver
{
    public function created(Client $client)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Client Created',
            'description' => "Client '{$client->client_name}' was created.",
        ]);
    }

    public function updated(Client $client)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Client Updated',
            'description' => "Client '{$client->client_name}' was updated.",
        ]);
    }

    public function deleted(Client $client)
    {
        Activity::create([
            'user_id' => Auth::id(),
            'event_type' => 'Client Deleted',
            'description' => "Client '{$client->client_name}' was deleted.",
        ]);
    }
}
