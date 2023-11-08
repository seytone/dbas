<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetRequest extends Notification
{
    use Queueable;

    protected $token;

    /**
    * Create a new notification instance.
    *
    * @return void
    */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url('/api/password/find/'.$this->token);
        return (new MailMessage)
            ->subject('Recuperación de contraseña')
            ->greeting('Hola!')
            ->line('Recibiste este correo porque recibimos una solicitud de recuperación de contraseña para tu cuenta.')
            ->action('Resetear Contraseña', url($url))
            ->line('Si no fuiste tú quien lo solicitó, ninguna acción de tu parte es requerida.')
            ->salutation('Cordialmente,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
