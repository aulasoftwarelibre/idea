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

namespace App\Event;

use App\Entity\Idea;
use App\Entity\User;
use App\Event\Abstracts\AbstractIdeaEvent;

final class IdeaWasVotedEvent extends AbstractIdeaEvent
{
    public function __construct(Idea $idea, private User $voter)
    {
        parent::__construct($idea);
    }

    public function getVoter(): User
    {
        return $this->voter;
    }
}
