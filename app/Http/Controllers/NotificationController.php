<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function handleEvent(Request $request)
    {
        if ($event === 'task.status.updated') {
            // Handle the task status updated event
            tatusUpdated($request);
        } elseif ($event == 'user.activated') {
            // Handle the user activated event
            $this->handleUserActivated($request);
        } elseif ($event == 'user.deactivated') {
            // Handle the user deactivated event
            $this->handleUserDeactivated($request);
        } else {
            return response()->json(['message' => 'Event not recognized'], 400);
        }

        
        // For example, you can log the event or send a notification
        \Log::info('Event received:', $request->all());

        return response()->json(['message' => 'Event handled successfully']);
    }
}
