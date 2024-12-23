<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Reglamento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Reglas extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user,Reglamento $reglas)
    {
        $this->user = $user;
        $this->reglas = $reglas;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('reservafacil@gmail.com', 'Reserva Facil')
        ->subject('Reglamento actualizado')
        ->view('emails.email_reglas')
        ->with([
            'user' => $this->user,
            'reglas' => $this->reglas,
        ]);
    }
}
