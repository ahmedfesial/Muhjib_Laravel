<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityController extends Controller
{
    public function index()
    {
        $logs = Activity::with('user')
            ->latest()
            ->take(50)
            ->get()
            ->map(function ($log) {
                return [
                    'type' => $log->event_type ?? 'Activity',
                    'user' => $log->user->name ?? 'System',
                    'description' => $log->description,
                    'time' => Carbon::parse($log->created_at)->format('M d, g:i A'),
                ];
            });

        return response()->json($logs);
    }
}
