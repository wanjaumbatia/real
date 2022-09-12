<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Shortage extends Mailable
{
    use Queueable, SerializesModels;

    public $handler;
    public $short;
    public $expected;
    public $submited;
    public $branch;
    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($handler, $short, $expected, $submited, $branch, $user)
    {
        $this->handler = $handler;
        $this->short = $short;
        $this->expected = $expected;
        $this->submited = $submited;
        $this->branch = $branch;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('shortage');
    }
}
