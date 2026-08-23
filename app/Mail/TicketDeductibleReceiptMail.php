<?php

namespace App\Mail;

use App\Models\RaffleTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TicketDeductibleReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;

    public function __construct(RaffleTicket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Notificación: Recibo Deducible Requerido - Boleto #{$this->ticket->number}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_deductible_receipt',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
