<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SolicitudRechazadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $reserva;
    public $motivo;
    public function __construct(User $user, Reserva $reserva, $motivo)
    {
        $this->user = $user;
        $this->reserva = $reserva;
        $this->motivo = $motivo;
    }

    public function build()
    {
        return $this->from('reservafacil@gmail.com', 'Reserva Facil')
            ->subject('Reserva rechazada')
            ->view('emails.email_reserva_rechazada')
            ->with([
                'user' => $this->user,
                'reserva' => $this->reserva,
                'motivo' => $this->motivo,
            ]);
    }
}
