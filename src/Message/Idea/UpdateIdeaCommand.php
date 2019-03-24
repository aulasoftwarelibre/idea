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

namespace App\Message\Idea;

use App\Entity\Group;
use App\Entity\Idea;

final class UpdateIdeaCommand
{
    /**
     * @var Idea
     */
    private $idea;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $description;
    /**
     * @var Group
     */
    private $group;

    public function __construct(
        Idea $idea,
        string $title,
        string $description,
        Group $group
    ) {
        $this->idea = $idea;
        $this->title = $title;
        $this->description = $description;
        $this->group = $group;
    }

    /**
     * @return Idea
     */
    public function getIdea(): Idea
    {
        return $this->idea;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }
}
