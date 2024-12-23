<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChangePwdMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->from('reservafacil@gmail.com', 'Farmacia ALVA')
            ->subject('Cambio de contraseña')
            ->view('emails.email_change_password')
            ->with([
                'user' => $this->user,
            ]);
    }
}
