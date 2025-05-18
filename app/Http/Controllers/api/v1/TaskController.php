<?php

namespace App\Http\Controllers\api\v1;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use App\Mail\TaskStatusUpdated;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TaskAssignedNotification;
use App\Notifications\TaskCompletedNotification;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::with(['assignedUser', 'createdBy', 'attachments'])->get();

        return response()->json($tasks);
    }

    public function getTaskById($id)
    {
        $task = Task::with(['assignedUser', 'createdBy', 'attachments'])->find($id);

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        return response()->json($task);
    }
    public function getTasksByUserId($userId)
    {
        $tasks = Task::with(['assignedUser', 'createdBy', 'attachments'])
            ->where('assigned_to', $userId)
            ->get();

        return response()->json($tasks);
    }
    public function getTasksByCreatedBy($userId)
    {
        $tasks = Task::with(['assignedUser', 'createdBy', 'attachments'])
            ->where('created_by', $userId)
            ->get();

        return response()->json($tasks);
    }
    public function getTasksByStatus($status)
    {
        $tasks = Task::with(['assignedUser', 'createdBy', 'attachments'])
            ->where('status', $status)
            ->get();

        return response()->json($tasks);
    }
    public function getTasksByPriority($priority)
    {
        $tasks = Task::with(['assignedUser', 'createdBy', 'attachments'])
            ->where('priority', $priority)
            ->get();

        return response()->json($tasks);
    }
    public function getTasksByDueDate($dueDate)
    {
        $tasks = Task::with(['assignedUser', 'createdBy', 'attachments'])
            ->where('due_date', $dueDate)
            ->get();

        return response()->json($tasks);
    }

    /**
     * Send email to users with completed tasks
     * This method is commented out as it may not be needed in the current context.
     */
    /*
    public function sendEmailToUsersWithCompletedTasks()
    {
        $completedTasks = Task::with(['assignedUser'])
            ->where('status', 'completed')
            ->get();

        foreach ($completedTasks as $task) {
            $user = $task->assignedUser;

            if ($user && $user->email) {
                Mail::to($user->email)->send(new \App\Mail\TaskCompletedMail($task));
            }
        }

        return response()->json(['message' => 'Emails sent to users with completed tasks']);
    } */


    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        // Validate the incoming request
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'assigned_to' => 'nullable|exists:users,id', // Ensure the user exists
        'status' => 'required|in:pending,in_progress,completed',
        'priority' => 'required|in:low,medium,high',
        'due_date' => 'nullable|date',
        'comments' => 'nullable|string',
    ]);

    // Create the task
    $task = Task::create([
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'assigned_to' => $validated['assigned_to'] ?? null,
        'created_by' => $request->user()->id, // The currently authenticated user
        'status' => $validated['status'] ?? 'Pending', // Use 'Pending' as the default status if not provided in the request

        'priority' => $validated['priority'],
        'due_date' => $validated['due_date'] ?? null,
        'comments' => $validated['comments'] ?? null,
    ]);

    /* Notify the assigned user */
    if ($task->assigned_to) {
        $assignedUser = User::find($task->assigned_to);
        if ($assignedUser) {
            $assignedUser->notify(new TaskAssignedNotification($task));
        }
    }

    return response()->json([
        'message' => 'Task created successfully',
        'task' => $task,
    ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function showAssignedTasks(Request $request)
    {
        $user = $request->user(); // Get the logged-in user

        $tasks = Task::where('assigned_to', $user->id)->get(); // Get tasks assigned to the authenticated user

        return response()->json($tasks);
    }

    /**
     * Show the form for updating the specified resource.
     */
    public function updateStatus(Request $request, $taskId)
    {
        // Get the authenticated user
        $user = $request->user(); // Get the logged-in user

        // Log the incoming request data
        Log::info('Task status update attempt', [
        'user_id' => $request->user()->id,
        'task_id' => $taskId,
        'status' => $request->input('status'),
]);



        // Validate the request
        $request->validate([
            'status' => 'required|string|in:pending,in-progress,completed',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:20480', // Optional attachment validation, max size 20MB
        ]);

        // Fetch the task explicitly using the ID
        $task = Task::find($taskId);

        // Check if the task exists
        if (!$task) {
            return response()->json([
                'success' => false,
                'message' => 'Task not found.'
            ], 404);
        }
        
        // Check if the authenticated user is assigned to this task
        if ($task->assigned_to !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this task.'
                 //'message' => 'User with ID ' . $user . ' is not authorized to update this task, rather it is assigned to user with ID ' . $task->assigned_to
            ], 403);
        }

        // Handle the optional attachment
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $filePath = $file->store('attachments', 'public'); // Store the file in the 'attachments' directory

            // Create an attachment record
            $task->attachments()->create(['file_path' => $filePath]);
        }
        // Update the status
        $task->update(['status' => $request->status]);

         // Check if the task is completed
        if ($request->status === 'completed') {
        // Fetch all users with the role 'admin'
        $admins = User::where('role', 'admin')->get();

            // Send an email notification to each admin
            Notification::send($admins, new TaskCompletedNotification($task));
           
        }
        return response()->json([
            'success' => true,
            'message' => 'Task status updated successfully.',
            'task' => $task
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
