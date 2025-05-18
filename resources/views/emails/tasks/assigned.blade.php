<x-mail::message>
# New Task Assigned

Hi ${task->assignedTo ?? 'N/A'},
You have been assigned a new task: **{{ $task->title }}**.

## Task Details:
- **Title:** {{ $task->title }}
- **Description:** {{ $task->description ?? 'No description provided' }}
- **Priority:** {{ $task->priority }}
- **Due Date:** {{ $task->due_date ?? 'No due date set' }}

Please log in to the system to view and manage your task.

<x-mail::button :url="''">
🔍 View Task
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
