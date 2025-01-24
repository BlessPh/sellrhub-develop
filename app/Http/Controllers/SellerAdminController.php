<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

class SellerAdminController extends Controller
{
    public function notifications()
    {
        $user = Auth::user();

        $notifications = $user->notifications;

        return response()->json($notifications);
    }

    public function unreadNotifications()
    {
        $user = Auth::user();

        $notifications = $user->unreadNotifications;

        return response()->json($notifications);
    }

    public function markNotificationAsRead($notificationId)
    {
        $user = Auth::user();

        $notification = $user->notifications()->findOrFail($notificationId);

        $notification->markAsRead();

        return response()->json([
            'success' => true,
        ]);
    }


}
