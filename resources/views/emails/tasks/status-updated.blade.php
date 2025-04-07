@component('mail::message')
# Task Status Updated

Hello Admin,

The status of a task assigned to **{{ $task->assignedTo ?? 'N/A' }}** has been updated.

---

### 📋 Task Details:

- **Title:** {{ $task->title }}
- **Description:** {{ $task->description ?? 'No description provided' }}
- **Status:** {{ ucfirst($task->status) }}
- **Due Date:** {{ \Carbon\Carbon::parse($task->due_date)->toFormattedDateString() ?? 'Not set' }}

---

@component('mail::button', ['url' => url('/tasks/' . $task->id)])
🔍 View Task
@endcomponent

If you have any questions, please follow up with the assigned user or project manager.

Thanks,  
{{ config('app.name') }}
@endcomponent
