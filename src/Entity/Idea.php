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

use App\Services\Seo\OpenGraphItemInterface;
use App\Utils\StringUtils;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use const PHP_INT_MAX;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IdeaRepository")
 * @ORM\Table()
 *
 * @Assert\Expression(
 *     "(this.getStartsAt() === this.getEndsAt()) or (this.getStartsAt() !== null and this.getEndsAt() > this.getStartsAt())",
 *     message="error.idea_end_date"
 * )
 * @Vich\Uploadable()
 */
class Idea implements OpenGraphItemInterface
{
    public const UNLIMITED_SEATS = PHP_INT_MAX;
    public const LIMITLESS       = 0;
    // Format consts
    public const FACE_TO_FACE = 'FACE_TO_FACE';
    public const ONLINE       = 'ONLINE';
    public const STREAMING    = 'STREAMING';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Version()
     */
    private int $version;

    /**
     * @ORM\Column(length=255)
     *
     * @Assert\Length(min="10", max="255")
     * @Assert\NotBlank()
     * @Groups("read")
     */
    private string $title;

    /**
     * @ORM\Column(type="text")
     *
     * @Assert\Length(min="10")
     * @Assert\NotBlank()
     * @Groups("read")
     */
    private string $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @Groups("read")
     */
    private bool $closed;

    /**
     * @ORM\Column(length=255, unique=true)
     *
     * @Gedmo\Slug(fields={"title"}, unique=true, updatable=false)
     * @Groups("read")
     */
    private string $slug;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     * @Groups("read")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     * @Groups("read")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="ideas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $owner;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="idea", cascade={"persist", "remove"}, orphanRemoval=true)
     *
     * @var Vote[]|Collection
     */
    private Collection $votes;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Group", inversedBy="ideas")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     *
     * @Groups("read")
     */
    private Group $group;

    /**
     * @ORM\Column(type="integer", name="num_seats")
     *
     * @Assert\Range(min="0")
     * @Groups("read")
     */
    private int $numSeats;

    /**
     * @ORM\Column(type="integer", name="external_num_seats")
     *
     * @Assert\Range(min=0)
     * @Groups("read")
     */
    private int $externalNumSeats;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups("read")
     */
    protected ?DateTime $startsAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @Groups("read")
     */
    protected ?DateTime $endsAt = null;

    /**
     * @ORM\Column(length=255, nullable=true)
     *
     * @Assert\Length(max="255")
     * @Groups("read")
     */
    protected ?string $location = null;

    /**
     * @ORM\Column(type="string", options={"default"="FACE_TO_FACE"})
     *
     * @Assert\Choice(callback="getFormats")
     * @Groups("read")
     * @psalm-property Idea::FACE_TO_FACE|Idea::ONLINE|Idea::STREAMING
     */
    private string $format;

    /** @ORM\Column(type="string", nullable=true) */
    protected ?string $jitsiLocatorRoom = null;

    /**
     * @ORM\Column(type="boolean", options={"default"=false})
     *
     * @Groups("read")
     */
    protected bool $isJitsiRoomOpen;


    /** @ORM\Column(type="boolean") */
    private bool $private;

    /** @ORM\Column(type="boolean") */
    private bool $internal;

    /**
     * @Vich\UploadableField(
     *     mapping="ideas",
     *     fileNameProperty="image.name",
     *     size="image.size",
     *     mimeType="image.mimeType",
     *     originalName="image.originalName"
     * )
     * @Assert\Image(
     *     minHeight=600,
     *     minWidth=1200,
     *     minRatio=2,
     *     maxRatio=2,
     * )
     */
    private File|UploadedFile|null $imageFile = null;

    /** @ORM\Embedded(class="Vich\UploaderBundle\Entity\File") */
    private EmbeddedFile $image;

    /** @ORM\Column(type="boolean", options={"default"=false}) */
    private bool $highlight = false;

    public function __construct()
    {
        $this->votes            = new ArrayCollection();
        $this->closed           = false;
        $this->private          = false;
        $this->internal         = false;
        $this->numSeats         = self::LIMITLESS;
        $this->format           = self::FACE_TO_FACE;
        $this->externalNumSeats = 0;
        $this->jitsiLocatorRoom = null;
        $this->isJitsiRoomOpen  = false;
        $this->version          = 1;
        $this->image            = new EmbeddedFile();
    }

    public static function with(string $title, string $description, User $owner, Group $group): self
    {
        $idea = new self();

        $idea->title       = $title;
        $idea->description = $description;
        $idea->owner       = $owner;
        $idea->group       = $group;

        return $idea;
    }

    /**
     * @return array<string, string>
     */
    public static function getFormats(): array
    {
        return [
            'Presencial' => self::FACE_TO_FACE,
            'Online' => self::ONLINE,
            'Streaming' => self::STREAMING,
        ];
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isClosed(): bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): self
    {
        $this->owner = $owner;
        $this->addVote(Vote::create($this, $owner));

        return $this;
    }

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(Group $group): self
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return Collection<int,Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    /**
     * @return Collection<int,Vote>
     */
    public function getExternalVotes(): Collection
    {
        return $this->votes->filter(static function (Vote $vote) {
            return $vote->getUser()->isExternal();
        });
    }

    public function addVote(Vote $vote): self
    {
        $vote->setIdea($this);

        $found = $this->votes->filter(static function (Vote $item) use ($vote) {
            return $item->getUser()->equalsTo($vote->getUser());
        });

        if ($found->isEmpty()) {
            $this->votes[] = $vote;
        }

        return $this;
    }

    public function removeVote(Vote $vote): void
    {
        $this->votes->removeElement($vote);
    }

    public function getNumSeats(): int
    {
        return $this->numSeats;
    }

    public function setNumSeats(int $numSeats): self
    {
        $this->numSeats = $numSeats;

        return $this;
    }

    public function hasLimitedSeats(): bool
    {
        return $this->numSeats !== 0;
    }

    public function countFreeSeats(): int
    {
        if (! $this->hasLimitedSeats()) {
            return self::UNLIMITED_SEATS;
        }

        return $this->numSeats - $this->votes->count();
    }

    public function getExternalNumSeats(): int
    {
        return $this->externalNumSeats;
    }

    public function setExternalNumSeats(int $externalNumSeats): self
    {
        $this->externalNumSeats = $externalNumSeats;

        return $this;
    }

    public function hasLimitedSeatsToExternal(): bool
    {
        if ($this->isInternal()) {
            return true;
        }

        return $this->getExternalNumSeats() > 0;
    }

    public function countFreeExternalSeats(): int
    {
        if ($this->isInternal()) {
            return 0;
        }

        if (! $this->hasLimitedSeatsToExternal()) {
            return $this->countFreeSeats();
        }

        $maxAvailableExternalSeats = $this->externalNumSeats - $this->getExternalVotes()->count();
        $remainingSeats            = $this->countFreeSeats();

        return $maxAvailableExternalSeats < $remainingSeats ? $maxAvailableExternalSeats : $remainingSeats;
    }

    public function getStartsAt(): ?DateTime
    {
        return $this->startsAt;
    }

    public function setStartsAt(?DateTime $startsAt): self
    {
        $this->startsAt = $startsAt;

        return $this;
    }

    public function getEndsAt(): ?DateTime
    {
        return $this->endsAt;
    }

    public function setEndsAt(?DateTime $endsAt): self
    {
        $this->endsAt = $endsAt;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function isFaceToFace(): bool
    {
        return $this->format === self::FACE_TO_FACE;
    }

    public function isOnline(): bool
    {
        return $this->format === self::ONLINE;
    }

    public function isStreaming(): bool
    {
        return $this->format === self::STREAMING;
    }

    /**
     * @psalm-return Idea::FACE_TO_FACE|Idea::ONLINE|Idea::STREAMING
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * @psalm-param Idea::FACE_TO_FACE|Idea::ONLINE|Idea::STREAMING $format
     */
    public function setFormat(string $format): Idea
    {
        $this->format = $format;

        if (! $this->jitsiLocatorRoom && $this->isOnline()) {
            $this->setJitsiLocatorRoom(StringUtils::locator());
        }

        if (! $this->isOnline()) {
            $this->setJitsiLocatorRoom(null);
        }

        return $this;
    }

    public function getJitsiLocatorRoom(): ?string
    {
        return $this->jitsiLocatorRoom;
    }

    public function setJitsiLocatorRoom(?string $jitsiLocatorRoom): self
    {
        $this->jitsiLocatorRoom = $jitsiLocatorRoom;

        return $this;
    }

    public function isJitsiRoomOpen(): bool
    {
        return $this->isJitsiRoomOpen;
    }

    public function setIsJitsiRoomOpen(bool $isJitsiRoomOpen): self
    {
        $this->isJitsiRoomOpen = $isJitsiRoomOpen;

        return $this;
    }

    public function isPrivate(): bool
    {
        return $this->private;
    }

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

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $image
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if (! $image) {
            return;
        }

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updatedAt = new DateTime();
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?EmbeddedFile
    {
        return $this->image;
    }

    public function getHighlight(): ?bool
    {
        return $this->highlight;
    }

    public function setHighlight(bool $highlight): self
    {
        $this->highlight = $highlight;

        return $this;
    }
}
