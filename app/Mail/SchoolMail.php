<?php

namespace App\Mail;

use App\Classes\Settings\CommunicationSettings;
use App\Models\People\Person;
use App\Models\Utilities\SchoolMessage;
use App\Notifications\LmsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Transport\ArrayTransport;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;
use RuntimeException;


class SchoolMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    public SchoolMessage $schoolMessage;

    /**
     * Create a new message instance.
     */
    public function __construct(public LmsNotification $notification)
    {
        $this->schoolMessage = $notification->message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $commSettings = app(CommunicationSettings::class);
        return new Envelope(
            from: new Address($commSettings->email_from_address, $commSettings->email_from),
            subject: $this->schoolMessage->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            htmlString: Blade::render($this->schoolMessage->body, $this->notification->withTokens()),
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $files = $this->schoolMessage->workFiles()
            ->visible()
            ->get();
        $attachments = [];
        foreach($files as $file)
            $attachments[] = Attachment::fromData(fn() => $file->storageInstance()
                ->fileContents($file), $file->name)
                ->withMime($file->mime);
        return $attachments;
    }

	public function mailableToRfc2822(Person|string $recipient): string
	{
		$settings = app(CommunicationSettings::class);
		$transport = new ArrayTransport();

		$mailer = new Mailer(
			'array-stringify',
			app('view'),
			$transport,
			app('events'),
		);

		$mailer->alwaysFrom($settings->email_from_address, $settings->email_from);

		$mailer->alwaysReplyTo($settings->email_from_address, $settings->email_from);
		$this->to($recipient instanceof Person ? $recipient->email : $recipient);

		clone($this)->send($mailer);

		$sent = $transport->messages()->last();

		if ($sent === null) {
			throw new RuntimeException('Failed to build mailable.');
		}

		return $sent->getOriginalMessage()->toString();
	}
}
