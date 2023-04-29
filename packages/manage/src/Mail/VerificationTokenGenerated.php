<?php

namespace Deyji\Manage\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Auth\Authenticatable;

class VerificationTokenGenerated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $user;
    public $subject;
    public $address;
    public $name;
    public function __construct(Authenticatable $user, $subject = null, $address = null, $name = null)
    {
        //
        $this->user = $user;
        $this->subject = $subject;
        $this->address = $address;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('manage::email');
    }
}
