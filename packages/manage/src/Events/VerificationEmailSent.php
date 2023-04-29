<?php

namespace Deyji\Manage\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class VerificationEmailSent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Authenticatable $user)
    {
        // Assign the class attribute
        $this->user = $user;
    }

}
