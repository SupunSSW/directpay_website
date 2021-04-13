<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class walletNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $message;
    public $state;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($title, $message, $state)
    {
        $this->title = $title;
        $this->message = $message;
        $this->state = $state;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('email.wallet')
            ->subject($this->title);
    }
}
