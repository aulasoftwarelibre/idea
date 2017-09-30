<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Idea.
 *
 * @ORM\Entity(repositoryClass="App\Repository\IdeaRepository")
 * @ORM\Table()
 */
class Idea
{
    const STATE_PROPOSED = 'proposed';
    const STATE_REJECTED = 'rejected';
    const STATE_APPROVED = 'approved';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     * @Assert\Length(min="10", max="255")
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\Length(min="10")
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $closed;

    /**
     * @var string
     * @ORM\Column(length=32)
     * @Assert\Choice(callback="getStates")
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(length=255, unique=true)
     * @Gedmo\Slug(fields={"title"}, unique=true, updatable=false)
     */
    private $slug;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ideas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var Vote[]
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="idea", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $votes;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="ideas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $group;

    public static function getStates(): array
    {
        return [
            'Propuesta' => static::STATE_PROPOSED,
            'Rechazada' => static::STATE_REJECTED,
            'Aceptada' => static::STATE_APPROVED,
        ];
    }

    /**
     * Idea constructor.
     */
    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->closed = false;
        $this->state = static::STATE_PROPOSED;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title ?? '';
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return Idea
     */
    public function setTitle(string $title): Idea
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return Idea
     */
    public function setDescription(string $description): Idea
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return bool
     */
    public function isClosed(): bool
    {
        return $this->closed;
    }

    /**
     * @param bool $closed
     *
     * @return Idea
     */
    public function setClosed(bool $closed): Idea
    {
        $this->closed = $closed;

        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     *
     * @return Idea
     */
    public function setState(string $state): Idea
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return Idea
     */
    public function setCreatedAt(\DateTime $createdAt): Idea
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Idea
     */
    public function setUpdatedAt(\DateTime $updatedAt): Idea
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner(): ?User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     *
     * @return Idea
     */
    public function setOwner(User $owner): Idea
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup(): ?Group
    {
        return $this->group;
    }

    /**
     * @param Group $group
     *
     * @return Idea
     */
    public function setGroup(Group $group): Idea
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return Collection|Vote[]
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote
     *
     * @return Idea
     */
    public function addVote(Vote $vote): Idea
    {
        $vote->setIdea($this);
        $this->votes[] = $vote;

        return $this;
    }

    /**
     * @param Vote $vote
     */
    public function removeVote(Vote $vote)
    {
        $this->votes->removeElement($vote);
    }
}
