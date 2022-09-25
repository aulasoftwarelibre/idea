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

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

use function assert;
use function sprintf;

#[ORM\Table]
#[ORM\Entity]
class Vote
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private int $id;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\User', inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Idea', inversedBy: 'votes')]
    #[ORM\JoinColumn(nullable: false)]
    private Idea $idea;

    public static function create(Idea $idea, User $user): self
    {
        $vote = new self();
        $vote->setIdea($idea);
        $vote->setUser($user);

        return $vote;
    }

    public function __toString(): string
    {
        $user = $this->getUser();
        assert($user instanceof User);

        return sprintf('%s [%s]', $user->getFullname(), $user->getUsername());
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdea(): Idea
    {
        return $this->idea;
    }

    public function setIdea(Idea $idea): self
    {
        $this->idea = $idea;

        return $this;
    }
}
