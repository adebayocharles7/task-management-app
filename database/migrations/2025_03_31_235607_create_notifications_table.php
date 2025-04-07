<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User receiving the notification
            $table->string('type'); // Type of notification (e.g., task assigned, task updated)
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade'); // Related task
            $table->text('message'); // Notification message
            $table->boolean('is_read')->default(false); // Read status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
