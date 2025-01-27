<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvoiceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $license;
    public $pdf;

    public function __construct($license, $pdf)
    {
        $this->license = $license;
        $this->pdf = $pdf;
    }

    public function build()
    {
        return $this->view('emails.invoice')
                    ->subject('Your Invoice')
                    ->from('kairaoii@mfmrd.gov.ki', 'Coastal Fisheries Licensing System')
                    ->replyTo('info@mfmrd.gov.ki', 'Your Name')
                    ->attachData($this->pdf->output(), 'invoice.pdf');
    }
}