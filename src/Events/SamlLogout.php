<?php namespace SimplerSaml\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\User;

class SamlLogout
{
    use SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @return \SimplerSaml\Events\SamlLogout
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
