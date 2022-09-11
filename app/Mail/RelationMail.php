<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RelationMail extends Mailable
{
    use Queueable, SerializesModels;

    private $content = '';
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $content)
    {
        $this->content = $content;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('contract@FIFA.com', 'FIFA')
            ->subject('Contract Update')
            ->html($this->content);
    }
}
