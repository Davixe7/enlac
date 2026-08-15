<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeductibleReceiptRequested extends Mailable
{
    use Queueable, SerializesModels;

    public Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model->loadMissing(['donor', 'sponsor']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Solicitud de Recibo Deducible - ENLAC',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deductible_receipt_requested',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
