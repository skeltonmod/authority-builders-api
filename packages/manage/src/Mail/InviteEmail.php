<?php
namespace Deyji\Manage\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class InviteEmail extends Mailable{
    use Queueable, SerializesModels;

    public $email;
    public $token;
    public function __construct($email)
    {
        //
        $this->email = $email;    
        $this->token = Hash::make($email);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('manage::invite_email');
    }
}