<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TwoFactorCodeMail extends Mailable
{
    public function __construct(public string $code) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Tu código de verificación');
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "<p>Tu código de verificación es <b>{$this->code}</b>.</p><p>Vence en 5 minutos.</p>"
        );
    }
}