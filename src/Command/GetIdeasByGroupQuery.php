<?php

namespace App\Command;

use App\Entity\User;

class GetIdeasByGroupQuery
{

    private $page;

    private $user;

    public function __construct(int $page = 1, User $user)
    {
        $this->page = $page;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }
}