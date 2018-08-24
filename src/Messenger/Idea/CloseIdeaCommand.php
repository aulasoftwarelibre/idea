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

namespace App\Messenger\Idea;

use App\Entity\Idea;

final class CloseIdeaCommand
{
    /**
     * @var Idea
     */
    private $idea;
    /**
     * @var bool
     */
    private $closed;

    /**
     * CloseIdeaCommand constructor.
     */
    public function __construct(Idea $idea, bool $closed = true)
    {
        $this->idea = $idea;
        $this->closed = $closed;
    }

    /**
     * @return Idea
     */
    public function getIdea(): Idea
    {
        return $this->idea;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }
}
