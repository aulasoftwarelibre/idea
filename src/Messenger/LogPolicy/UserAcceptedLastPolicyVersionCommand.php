<?php


namespace App\Messenger\LogPolicy;


use App\Entity\User;

class UserAcceptedLastPolicyVersionCommand
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}