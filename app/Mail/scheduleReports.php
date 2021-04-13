<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class scheduleReports extends Mailable
{
    use Queueable, SerializesModels;
    public $file;
    public $message;

    /**
     * Create a new message instance.
     *
     * @param $subject
     * @param $file
     * @param $message
     */
    public function __construct($subject, $file, $message)
    {
        $this->subject = $subject;
        $this->file = $file;
        $this->message = $message;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        \Log::info($this->file);
        \Log::info($this->message);

        $fileName = $this->file . '.xls';
        $filePath = storage_path('Excel/') . $fileName;

        return $this->markdown('email.scheduleReport')
            ->subject($this->message)
            ->attach($filePath);
    }
}
