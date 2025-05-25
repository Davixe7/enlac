<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\EvaluationSchedule;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EvaluationScheduled extends Notification
{
    use Queueable;

    protected $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
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

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $fechaEvaluacion = Carbon::parse( $this->appointment->date )->format('d/m/Y H:i:s');
        $actionUrl = env('SPA_URL', 'http://localhost:9000/#/') . 'notificaciones';

        return (new MailMessage)
        ->subject('Nueva evaluación agendada.')
        ->greeting('¡Hola!')
        ->line('Se te ha agendado una evaluación a traves de la plataforma')
        ->line($this->appointment->candidate->fullName)
        ->line($fechaEvaluacion)
        ->action('Ver Detalles', $actionUrl)
        ->salutation('Saludos, ENLAC');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $date = Carbon::parse( $this->appointment->date )->locale('es_MX')->isoFormat('dddd DD [de] MMMM [de] YYYY [a las] h:mm A');

        return [
            'title' => 'Evaluación agendada',
            'description' => 'Tienes una nueva evaluación pautada para el ' . $date,
        ];
    }
}
