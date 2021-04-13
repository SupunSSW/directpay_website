<?php

namespace App\Http\Controllers\Api;

use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgentController extends Controller
{
    public function notifications(Request $request)
    {
        $user = auth()->user();
        $notifications = Notification::query()->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return response()->json([
            'status' => 200,
            'data' => [
                "notifications" => $notifications
            ]
        ]);
    }
}
