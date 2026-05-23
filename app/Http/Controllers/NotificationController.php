<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::forUser($request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($notifications);
    }

    public function unreadCount(Request $request): JsonResponse
    {
        $count = Notification::forUser($request->user()->id)->unread()->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsRead();

        Log::info('Notification marked as read', [
            'notification_id' => $notification->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($notification);
    }

    public function markAsUnread(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->markAsUnread();

        Log::info('Notification marked as unread', [
            'notification_id' => $notification->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($notification);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $count = Notification::forUser($request->user()->id)->unread()->update([
            'read_at' => now(),
        ]);

        Log::info('All notifications marked as read', [
            'user_id' => $request->user()->id,
            'count' => $count,
        ]);

        return response()->json(['marked' => $count]);
    }

    public function destroy(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== $request->user()->id) {
            abort(403);
        }

        $notification->delete();

        Log::info('Notification deleted', [
            'notification_id' => $notification->id,
            'user_id' => $request->user()->id,
        ]);

        return response()->json([], 204);
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'action_url' => 'nullable|string|max:500',
        ]);

        $notification = Notification::create([
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'body' => $validated['body'] ?? null,
            'icon' => $validated['icon'] ?? 'bell',
            'action_url' => $validated['action_url'] ?? null,
        ]);

        broadcast(new NotificationSent($notification))->toOthers();

        Log::info('Notification sent', [
            'notification_id' => $notification->id,
            'user_id' => $validated['user_id'],
            'type' => $validated['type'],
        ]);

        return response()->json($notification, 201);
    }
}
