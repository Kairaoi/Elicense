<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ApplicantXMLMail extends Mailable
{
    use Queueable, SerializesModels;

    public $xmlFilePath;

    public function __construct($xmlFilePath)
    {
        $this->xmlFilePath = $xmlFilePath;
    }

    public function build()
    {
        return $this->subject('Your Application Details')
            ->attach($this->xmlFilePath, [
                'as' => 'applicant.xml',
                'mime' => 'application/xml',
            ])
            ->view('emails.applicant_notification');
    }
}
