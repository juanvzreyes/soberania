<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = Auth::user()->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return back()->with('success', 'Notificación marcada como leída');
    }

    public function markAllAsRead()
    {
        Auth::user()->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Todas las notificaciones marcadas como leídas');
    }

    public function getUnreadCount()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['count' => 0], 200);
            }
            $count = $user->notifications()
                ->where('is_read', false)
                ->count();

            return response()->json([
                'count' => $count,
                'notifications' => $user->notifications()
                    ->where('is_read', false)
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error fetching unread notifications: ' . $e->getMessage());
            return response()->json([
                'count' => 0,
                'error' => 'Error al cargar notificaciones'
            ], 200);
        }
    }
}
