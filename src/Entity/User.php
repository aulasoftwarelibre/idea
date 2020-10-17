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

use App\Validator;
use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use function sprintf;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 * @ORM\AttributeOverrides({
 *     @ORM\AttributeOverride(name="emailCanonical",
 *         column=@ORM\Column(
 *              name="email_canonical",
 *              type="string",
 *              length=255,
 *              nullable=true,
 *              unique=false,
 *         )
 *     ),
 *     @ORM\AttributeOverride(name="usernameCanonical",
 *         column=@ORM\Column(
 *              name="username_canonical",
 *              type="string",
 *              length=255,
 *              nullable=true,
 *              unique=false,
 *         )
 *     )
 * })
 *
 * @Gedmo\SoftDeleteable()
 * @Vich\Uploadable()
 * @Validator\Alias()
 */
class User extends BaseUser implements EquatableInterface
{
    public const STUDENT  = 'student';
    public const STAFF    = 'staff';
    public const TEACHER  = 'teacher';
    public const EXTERNAL = 'external';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     * @inheritdoc
     */
    protected $id;

    /** @ORM\Column(type="boolean") */
    protected bool $isExternal;

    /**
     * @ORM\Column(type="string", length=32)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=16)
     * @Assert\Regex("/[\w\d_]/u", message="form.label_alias_invalid")
     */
    protected ?string $alias = '';

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private int $version = 1;

    /**
     * @ORM\Column(length=32, nullable=true)
     *
     * @Assert\Choice(callback="getCollectives")
     * @Assert\NotBlank()
     */
    private ?string $collective = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Degree")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?Degree $degree = null;

    /**
     * @ORM\Column(length=4, nullable=true)
     *
     * @Assert\Regex("/\d{4}/")
     */
    private ?string $year = null;

    /** @ORM\Column(length=32, unique=false, nullable=true) */
    private ?string $nic = null;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Participation",
     *     mappedBy="user",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     *
     * @var Participation[]|Collection
     */
    private Collection $participations;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Idea",
     *     mappedBy="owner",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     *
     * @var Idea[]|Collection
     */
    private Collection $ideas;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Vote",
     *     mappedBy="user",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     *
     * @var Vote[]|Collection
     */
    private Collection $votes;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\LogPolicy",
     *     mappedBy="user"
     * )
     *
     * @var LogPolicy[]|Collection
     */
    private Collection $versions;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Comment",
     *     mappedBy="author",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     *
     * @var Comment[]|Collection
     */
    private Collection $comments;

    /**
     * @var File|UploadedFile|null
     * @Vich\UploadableField(
     *     mapping="avatars",
     *     fileNameProperty="image.name",
     *     size="image.size",
     *     mimeType="image.mimeType",
     *     originalName="image.originalName"
     * )
     */
    private $imageFile;

    /** @ORM\Embedded(class="Vich\UploaderBundle\Entity\File") */
    private EmbeddedFile $image;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     *
     * @var Collection<int,Group>
     * @inheritdoc
     */
    protected $groups;

    /** @ORM\Column(type="datetime", nullable=true) */
    private ?DateTimeInterface $deletedAt = null;

    public static function createUcoUser(string $username): self
    {
        $user = new self();
        $user
            ->setUsername($username)
            ->setEmail($username . '@uco.es')
            ->setPassword('!')
            ->setIsExternal(false)
            ->setEnabled(true);

        return $user;
    }

    public static function createExternalUser(string $email): self
    {
        $user = new self();
        $user
            ->setUsername($email)
            ->setEmail($email)
            ->setPassword('!')
            ->setIsExternal(true)
            ->setEnabled(true)
            ->setCollective(self::EXTERNAL);

        return $user;
    }

    public function __construct()
    {
        parent::__construct();

        $this->ideas          = new ArrayCollection();
        $this->votes          = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->image          = new EmbeddedFile();
    }

    public function __toString(): string
    {
        return sprintf(
            '%s %s [%s]',
            $this->getFirstname(),
            $this->getLastname(),
            $this->getUsername()
        );
    }

    public function equalsTo(?self $user): bool
    {
        if (! $user instanceof self) {
            return false;
        }

        return $this->getId() === $user->getId();
    }

    public function isExternal(): bool
    {
        return $this->isExternal;
    }

    public function setIsExternal(bool $isExternal): self
    {
        $this->isExternal = $isExternal;

        return $this;
    }

    public function getAlias(): string
    {
        return (string) $this->alias;
    }

    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return array<string, string>
     */
    public static function getCollectives(): array
    {
        return [
            'Estudiante' => self::STUDENT,
            'PDI' => self::TEACHER,
            'PAS' => self::STAFF,
            'Otros' => self::EXTERNAL,
        ];
    }

    public function getCollective(): ?string
    {
        return $this->collective;
    }

    public function setCollective(?string $collective): self
    {
        $this->collective = $collective;

        return $this;
    }

    /**
     * @return Collection<int,Idea>
     */
    public function getIdeas(): Collection
    {
        return $this->ideas;
    }

    public function addIdea(Idea $idea): self
    {
        $this->ideas[] = $idea;

        return $this;
    }

    public function removeIdea(Idea $idea): void
    {
        $this->ideas->removeElement($idea);
    }

    /**
     * @return Collection<int,Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function addVote(Vote $vote): self
    {
        $vote->setUser($this);
        $this->votes[] = $vote;

        return $this;
    }

    public function removeVote(Vote $vote): void
    {
        $this->votes->removeElement($vote);
    }

    /**
     * @return Collection<int,LogPolicy>
     */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(LogPolicy $version): self
    {
        $version->setUser($this);
        $this->versions[] = $version;

        return $this;
    }

    public function getDegree(): ?Degree
    {
        return $this->degree;
    }

    public function setDegree(?Degree $degree): self
    {
        $this->degree = $degree;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;

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

    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    public function getNic(): ?string
    {
        return $this->nic;
    }

    public function setNic(?string $nic): self
    {
        $this->nic = $nic;

        return $this;
    }

    /**
     * @return Collection<int,Participation>
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    public function addParticipation(Participation $participation): self
    {
        $participation->setUser($this);
        $this->participations[] = $participation;

        return $this;
    }

    public function removeParticipation(Participation $participation): void
    {
        $this->participations->removeElement($participation);
    }

    /**
     * @return Collection<int,Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof self
            && $user->getId() === $this->getId();
    }

    public function getDeletedAt(): ?DateTimeInterface
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeInterface $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function getVersion(): int
    {
        return $this->version;
    }

    public function setVersion(int $version): void
    {
        $this->version = $version;
    }
}
