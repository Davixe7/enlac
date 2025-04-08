<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    protected $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $spaUrl = env('SPA_URL', 'http://localhost:9000');
        $resetUrl = "{$spaUrl}/#/restablecer-contrasena/?token={$this->token}&email=" . urlencode($notifiable->email);

        return (new MailMessage)
        ->subject('Restablecer tu contraseña')
        ->greeting('¡Hola!')
        ->line('Has solicitado restablecer tu contraseña.')
        ->action('Restablecer Contraseña', $resetUrl)
        ->line('Si no solicitaste esta acción, puedes ignorar este correo.')
        ->salutation('Saludos, ENLAC');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
