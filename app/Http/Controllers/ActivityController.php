<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ActivityController extends Controller
{
public function index(Request $request)
    {
        $user = $request->user();

        $query = Activity::with('user')->latest();

        // لو اليوزر مش سوبر أدمن، نفلتر على الأنشطة الخاصة به فقط
        if (!$user->hasRole('super_admin')) {
            $query->where('user_id', $user->id);
        }

        $logs = $query->take(5)->get()->map(function ($log) {
            return [
                'type' => $log->event_type ?? 'Activity',
                'user' => $log->user->name ?? 'System',
                'description' => $log->description,
                'time' => optional($log->created_at)->format('M d, g:i A'),
            ];
        });

        return response()->json($logs);
    }


}
