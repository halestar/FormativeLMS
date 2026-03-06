<?php

namespace App\Mail;

use App\Models\Substitutes\Substitute;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewSubstituteWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public Substitute $substitute;

    /**
     * Create a new message instance.
     */
    public function __construct(Substitute $substitute)
    {
        $this->substitute = $substitute->loadMissing('campuses:id,name');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to New Roads Substitutes',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'substitutes.mails.new-sub-welcome',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
