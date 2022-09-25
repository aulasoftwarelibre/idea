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

namespace App\Event\Abstracts;

use App\Entity\Idea;
use Symfony\Contracts\EventDispatcher\Event;

class AbstractIdeaEvent extends Event
{
    public function __construct(private Idea $idea)
    {
    }

    public function getIdea(): Idea
    {
        return $this->idea;
    }
}
