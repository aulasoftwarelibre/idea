<?php

declare(strict_types=1);

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Message\Vote;

use App\Entity\Idea;
use App\Entity\User;

final class AddVoteCommand
{
    public function __construct(private Idea $idea, private User $user)
    {
    }

    public function getIdea(): Idea
    {
        return $this->idea;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
