<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notifications = $user
            ->notifications()
            ->latest()
            ->paginate(20);

        $user->unreadNotifications()->update(['read_at' => now()]);

        return view('student.notifications.index', compact('notifications'));
    }

    public function markRead(string $id)
    {
        $user = Auth::user();

        $user
            ->notifications()
            ->where('id', $id)
            ->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        $user = Auth::user();
        $user->unreadNotifications()->update(['read_at' => now()]);

        return response()->json(['ok' => true]);
    }

    public function unreadCount()
    {
        $user = Auth::user();

        return response()->json([
            'count' => $user->unreadNotifications()->count(),
        ]);
    }

    public function recent()
    {
        $user = Auth::user();

        $items = $user
            ->notifications()
            ->whereNull('read_at')
            ->latest()
            ->take(8)
            ->get()
            ->map(fn($n) => [
                'id'      => $n->id,
                'data'    => $n->data,
                'read'    => !is_null($n->read_at),
                'time'    => $n->created_at->diffForHumans(),
            ]);

        return response()->json($items);
    }
}
