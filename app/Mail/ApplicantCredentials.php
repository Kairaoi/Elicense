<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $password;
    public $username;

    public function __construct($applicant, $password, $username)
    {
        $this->applicant = $applicant;
        $this->password = $password;
        $this->username = $username;
    }

    public function build()
    {
        return $this->markdown('emails.applicant.credentials')
                    ->subject('Your Application Login Credentials');
    }
}
