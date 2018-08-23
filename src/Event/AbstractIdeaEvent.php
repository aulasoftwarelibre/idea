<?php

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
use Symfony\Component\EventDispatcher\Event;

class AbstractIdeaEvent extends Event
{
    /**
     * @var Idea
     */
    private $idea;

    public function __construct(Idea $idea)
    {
        $this->idea = $idea;
    }

    /**
     * @return Idea
     */
    public function getIdea(): Idea
    {
        return $this->idea;
    }
}
