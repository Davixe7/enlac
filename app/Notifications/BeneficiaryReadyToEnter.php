<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Candidate;

class BeneficiaryReadyToEnter extends Notification
{
    use Queueable;
    protected $candidate;

    /**
     * Create a new notification instance.
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $actionUrl = env('SPA_URL', 'http://localhost:9000/#/') . 'beneficiarios';

        return (new MailMessage)
            ->subject("{$this->candidate->full_name} listo para ingresar")
            ->greeting('¡Hola Coordinación Física!')
            ->line("Por medio del presente correo, comunicamos que el beneficiario {$this->candidate->full_name} ya reúne todos los requisitos administrativos y está listo para ingresar.")
            ->line("Solicitamos que se capture su Plan Individual en el sistema y programar la fecha de ingreso.")
            ->action('Ir a Beneficiarios', $actionUrl)
            ->salutation('Saludos, ENLAC');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Beneficiario listo para ingresar',
            'description' => "El beneficiario {$this->candidate->full_name} está listo para ingresar.",
            'candidate_id' => $this->candidate->id,
        ];
    }
}
