<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public $task; // Make it accessible in the email view

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $this->task = $task;
    }

    /**
     * Build the message.
     */
    public function envelope(): Envelope
    {
        return $this->subject('Task Status Updated');

        
    }

    /**
     * Define the content of the email.
     */
    public function content(): Content
    {
        return $this->markdown('emails.tasks.status-updated');
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // You can attach files here, if necessary
        return [];
    }
}
