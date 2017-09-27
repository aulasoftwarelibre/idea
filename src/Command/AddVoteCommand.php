<?php
/**
 * This file is part of the ceo.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 * (c) Sergio Gómez <sergio@uco.es>
 * (c) Omar Sotillo <i32sofro@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Entity\Idea;
use App\Entity\User;

final class AddVoteCommand
{
    /**
     * @var Idea
     */
    private $idea;
    /**
     * @var User
     */
    private $user;

    /**
     * SupportsIdeaCommand constructor.
     *
     * @param Idea $idea
     * @param User $user
     */
    public function __construct(Idea $idea, User $user)
    {
        $this->idea = $idea;
        $this->user = $user;
    }

    /**
     * @return Idea
     */
    public function getIdea(): Idea
    {
        return $this->idea;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
