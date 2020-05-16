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

use App\Utils\StringUtils;
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
 * @Assert\Expression(
 *     "(this.getStartsAt() === this.getEndsAt()) or (this.getStartsAt() !== null and this.getEndsAt() > this.getStartsAt())",
 *     message="error.idea_end_date"
 * )
 */
class Idea
{
    public const RELATIVE_STATE_NEW = 'new';
    public const STATE_PROPOSED = 'proposed';
    public const STATE_REJECTED = 'rejected';
    public const STATE_APPROVED = 'approved';
    public const LIMITLESS = 0;

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Version()
     */
    private $version;

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
     * @var int
     * @ORM\Column(type="integer", name="external_num_seats")
     * @Assert\Range(min=0)
     * @Groups("read")
     */
    private $externalNumSeats;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Groups("read")
     */
    protected $startsAt;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     * @Groups("read")
     */
    protected $endsAt;

    /**
     * @var string|null
     * @ORM\Column(length=255, nullable=true)
     * @Assert\Length(max="255")
     * @Groups("read")
     */
    protected $location;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=false})
     * @Groups("read")
     */
    protected $isOnline;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    protected $jitsiLocatorRoom;

    /**
     * @var bool
     * @ORM\Column(type="boolean", options={"default"=false})
     * @Groups("read")
     */
    protected $isJitsiRoomOpen;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $private;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $internal;

    /**
     * Idea constructor.
     */
    public function __construct()
    {
        $this->title = null;
        $this->description = null;
        $this->owner = null;
        $this->group = null;
        $this->votes = new ArrayCollection();
        $this->closed = false;
        $this->private = false;
        $this->internal = false;
        $this->state = static::STATE_PROPOSED;
        $this->numSeats = self::LIMITLESS;
        $this->externalNumSeats = 0;
        $this->isOnline = false;
        $this->jitsiLocatorRoom = null;
        $this->isJitsiRoomOpen = false;
        $this->version = 1;
    }

    public static function with(string $title, string $description, User $owner, Group $group): self
    {
        $idea = new self();

        $idea->title = $title;
        $idea->description = $description;
        $idea->owner = $owner;
        $idea->group = $group;

        return $idea;
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
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }

    /**
     * @return Idea
     */
    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
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
    public function getDescription(): ?string
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
    public function getOwner(): ?User
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
    public function getGroup(): ?Group
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
     * @return Collection
     */
    public function getExternalVotes(): Collection
    {
        return $this->votes->filter(function (Vote $vote) {
            return $vote->getUser()->isExternal();
        });
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
     * @return int
     */
    public function getExternalNumSeats(): int
    {
        return $this->externalNumSeats;
    }

    /**
     * @return Idea
     */
    public function setExternalNumSeats(int $externalNumSeats): self
    {
        $this->externalNumSeats = $externalNumSeats;

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
     * @return \DateTime|null
     */
    public function getEndsAt(): ?\DateTime
    {
        return $this->endsAt;
    }

    /**
     * @return Idea
     */
    public function setEndsAt(?\DateTime $endsAt): self
    {
        $this->endsAt = $endsAt;

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
    public function isOnline(): bool
    {
        return $this->isOnline;
    }

    /**
     * @return Idea
     */
    public function setIsOnline(bool $isOnline): self
    {
        $this->isOnline = $isOnline;

        if ($isOnline && !$this->jitsiLocatorRoom) {
            $this->jitsiLocatorRoom = StringUtils::locator();
        }

        if (!$isOnline) {
            $this->jitsiLocatorRoom = null;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getJitsiLocatorRoom(): ?string
    {
        return $this->jitsiLocatorRoom;
    }

    /**
     * @return Idea
     */
    public function setJitsiLocatorRoom(?string $jitsiLocatorRoom): self
    {
        $this->jitsiLocatorRoom = $jitsiLocatorRoom;

        return $this;
    }

    /**
     * @return bool
     */
    public function isJitsiRoomOpen(): bool
    {
        return $this->isJitsiRoomOpen;
    }

    /**
     * @return Idea
     */
    public function setIsJitsiRoomOpen(bool $isJitsiRoomOpen): self
    {
        $this->isJitsiRoomOpen = $isJitsiRoomOpen;

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

    public function isInternal(): bool
    {
        return $this->internal;
    }

    public function setInternal(bool $internal): self
    {
        $this->internal = $internal;

        return $this;
    }
}
