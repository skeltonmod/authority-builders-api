<?php

namespace Deyji\Manage\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class InviteEmailSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $email;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        // Assign the class attribute
        $this->email = $email;
        
    }

}
