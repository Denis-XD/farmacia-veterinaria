<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudAceptadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reserva;
    public $asignado;
    public function __construct(User $user, Reserva $reserva, $asignado)
    {
        $this->user = $user;
        $this->reserva = $reserva;
        $this->asignado = $asignado;
    }

    public function build()
    {
        return $this->from('reservafacil@gmail.com', 'Reserva Facil')
            ->subject('Reserva aceptada')
            ->view('emails.email_reserva_aceptada')
            ->with([
                'user' => $this->user,
                'reserva' => $this->reserva,
                'asignado' => $this->asignado,
            ]);
    }
}
