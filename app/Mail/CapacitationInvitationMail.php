<?php

namespace App\Mail;

use App\Models\Capacitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CapacitationInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $capacitation;

    public function __construct(Capacitation $capacitation)
    {
        $this->capacitation = $capacitation;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Capacitación ENLAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.capacitation_invitation',
        );
    }
}
