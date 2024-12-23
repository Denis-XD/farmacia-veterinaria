<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('reservafacil@gmail.com', 'Reserva Facil')
            ->subject('Bienvenido a Reserva Facil')
            ->view('emails.email_template')
            ->with([
                'user' => $this->user,
            ]);
        //return $this->view('emails.email_template')->with('user', $this->user);
    }
}
