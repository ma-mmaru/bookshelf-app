<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications;

        return view('notifications.index', compact('notifications'));
    }

    public function read(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return redirect()->back()->with('success', '通知を既読にしました。');
    }
}