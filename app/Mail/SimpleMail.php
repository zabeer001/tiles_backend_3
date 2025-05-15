<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SimpleMail extends Mailable
{
    use Queueable, SerializesModels;

    public $fileUrl;

    public function __construct($fileUrl)
    {
        $this->fileUrl = $fileUrl;
    }

    public function build()
    {
        return $this->view('emails.simple')
            ->subject('Custom Tiles Image')
            ->with([
                'fileUrl' => $this->fileUrl,
            ]);
    }
}
