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

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Idea.
 *
 * @ORM\Entity(repositoryClass="App\Repository\IdeaRepository")
 * @ORM\Table()
 * @ApiResource(
 *     attributes={"pagination_items_per_page"=10},
 *     collectionOperations={"get"},
 *     itemOperations={"get"},
 *     normalizationContext={"groups"={"read","idea"}}
 * )
 * @ApiFilter(BooleanFilter::class, properties={"closed"})
 * @ApiFilter(SearchFilter::class, properties={"state": "exact"})
 */
class Idea
{
    public const RELATIVE_STATE_NEW = 'new';
    public const STATE_PROPOSED = 'proposed';
    public const STATE_REJECTED = 'rejected';
    public const STATE_APPROVED = 'approved';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ApiProperty(identifier=false)
     */
    private $id;

    public const LIMITLESS = 0;

    /**
     * @var string
     *
     * @ORM\Column(length=255)
     * @Assert\Length(min="10", max="255")
     * @Assert\NotBlank()
     * @Groups("read")
     */
    private $title;

    /**
     * @var string
     * @ORM\Column(type="text")
     * @Assert\Length(min="10")
     * @Assert\NotBlank()
     * @Groups("read")
     */
    private $description;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups("read")
     */
    private $closed;

    /**
     * @var string
     * @ORM\Column(length=32)
     * @Assert\Choice(callback="getStates")
     * @Groups("read")
     */
    private $state;

    /**
     * @var string
     * @ORM\Column(length=255, unique=true)
     * @Gedmo\Slug(fields={"title"}, unique=true, updatable=false)
     * @Groups("read")
     * @ApiProperty(identifier=true)
     */
    private $slug;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @Groups("read")
     */
    private $createdAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     * @Groups("read")
     */
    private $updatedAt;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ideas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $owner;

    /**
     * @var Vote[]|Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="idea", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $votes;

    /**
     * @var Group
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="ideas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     * @Groups("read")
     */
    private $group;

    /**
     * @var int
     * @ORM\Column(type="integer", name="num_seats")
     * @Assert\Range(min="0")
     * @Groups("read")
     */
    private $numSeats;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Groups("read")
     */
    protected $startsAt;

    /**
     * @var string|null
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(max="255")
     * @Groups("read")
     */
    protected $location;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * Idea constructor.
     */
    public function __construct(string $title, string $description, User $owner, Group $group)
    {
        $this->title = $title;
        $this->description = $description;
        $this->owner = $owner;
        $this->group = $group;

        $this->votes = new ArrayCollection();
        $this->closed = false;
        $this->private = false;
        $this->state = static::STATE_PROPOSED;
        $this->numSeats = self::LIMITLESS;
    }

    public static function getStates(): array
    {
        return [
            'Propuesta' => static::STATE_PROPOSED,
            'Rechazada' => static::STATE_REJECTED,
            'Aceptada' => static::STATE_APPROVED,
        ];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Idea
     */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Idea
     */
    public function setDescription(string $description): self
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
     * @return Idea
     */
    public function setClosed(bool $closed): self
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
     * @return Idea
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getRelativeState(): string
    {
        $diff = $this->createdAt->diff(new \DateTime());

        if ($diff->days <= 2) {
            return self::RELATIVE_STATE_NEW;
        }

        return $this->state;
    }

    /**
     * @return string
     */
    public function getSlug(): string
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
     * @return Idea
     */
    public function setCreatedAt(\DateTime $createdAt): self
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
     * @return Idea
     */
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @return Idea
     */
    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        $this->addVote(Vote::create($this, $owner));

        return $this;
    }

    /**
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }

    /**
     * @return Idea
     */
    public function setGroup(Group $group): self
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
     * @return Idea
     */
    public function addVote(Vote $vote): self
    {
        $vote->setIdea($this);

        $found = $this->votes->filter(function (Vote $item) use ($vote) {
            return $item->getUser()->equalsTo($vote->getUser());
        });

        if ($found->isEmpty()) {
            $this->votes[] = $vote;
        }

        return $this;
    }

    /**
     * @param Vote $vote
     */
    public function removeVote(Vote $vote): void
    {
        $this->votes->removeElement($vote);
    }

    /**
     * @return int
     */
    public function getNumSeats(): int
    {
        return $this->numSeats;
    }

    /**
     * @return Idea
     */
    public function setNumSeats(int $numSeats): self
    {
        $this->numSeats = $numSeats;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartsAt(): ?\DateTime
    {
        return $this->startsAt;
    }

    /**
     * @return Idea
     */
    public function setStartsAt(?\DateTime $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLocation(): ?string
    {
        return $this->location;
    }

    /**
     * @return Idea
     */
    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPrivate(): bool
    {
        return $this->private;
    }

    /**
     * @return Idea
     */
    public function setPrivate(bool $private): self
    {
        $this->private = $private;

        return $this;
    }
}
