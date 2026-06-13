<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NotificationMbg;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = NotificationMbg::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = NotificationMbg::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markRead(NotificationMbg $notification): JsonResponse
    {
        if ($notification->user_id === auth()->id()) {
            $notification->markAsRead();
        }
        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        NotificationMbg::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
