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
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use function array_unique;
use function in_array;
use function sprintf;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="fos_user", indexes={@ORM\Index(columns={"username"}), @ORM\Index(columns={"email"})})
 *
 * @Gedmo\SoftDeleteable()
 * @Vich\Uploadable()
 * @Validator\Alias()
 */
class User implements EquatableInterface, UserInterface
{
    public const STUDENT  = 'student';
    public const STAFF    = 'staff';
    public const TEACHER  = 'teacher';
    public const EXTERNAL = 'external';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /** @ORM\Column(type="boolean") */
    protected bool $isExternal;

    /**
     * @ORM\Column(type="string", length=180)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=180)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", length=180)
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=1, max=180)
     * @Assert\Email()
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="array")
     *
     * @Assert\Choice({"ROLE_USER", "ROLE_ADMIN", "ROLE_SUPER_ADMIN"}, multiple=true)
     *
     * @var string[]
     */
    private array $roles = [];

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
     * @var Collection<int,Participation>
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
     * @var Collection<int,Idea>
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
     * @var Collection<int,Vote>
     */
    private Collection $votes;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\LogPolicy",
     *     mappedBy="user"
     * )
     *
     * @var Collection<int,LogPolicy>
     */
    private Collection $versions;

    /**
     * @Vich\UploadableField(
     *     mapping="avatars",
     *     fileNameProperty="image.name",
     *     size="image.size",
     *     mimeType="image.mimeType",
     *     originalName="image.originalName"
     * )
     */
    private File|UploadedFile|null $imageFile = null;

    /** @ORM\Embedded(class="Vich\UploaderBundle\Entity\File") */
    private EmbeddedFile $image;

    /**
     * @ORM\ManyToMany(targetEntity=Group::class, inversedBy="users", cascade={"all"}, fetch="EAGER")
     * @ORM\JoinTable(name="fos_user_user_group")
     *
     * @var Collection<int, Group>
     */
    private Collection $groups;

    /** @ORM\Column(type="datetime", nullable=true) */
    private ?DateTimeInterface $deletedAt = null;

    /** @ORM\Column(type="boolean") */
    private bool $enabled = true;

    /** @ORM\Column(type="string", length=64, nullable=true) */
    private ?string $firstname;

    /** @ORM\Column(type="string", length=64, nullable=true) */
    private ?string $lastname;

    /** @ORM\Column(type="string", length=1000, nullable=true) */
    private ?string $biography;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private ?DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    private ?DateTime $updatedAt;

    /** @ORM\Column(type="datetime", nullable=true) */
    private ?DateTime $lastLogin;

    public static function createUcoUser(string $username): self
    {
        $user = new self();
        $user
            ->setUsername($username)
            ->setEmail($username . '@uco.es')
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
            ->setIsExternal(true)
            ->setEnabled(true)
            ->setCollective(self::EXTERNAL);

        return $user;
    }

    public function __construct()
    {
        $this->ideas          = new ArrayCollection();
        $this->votes          = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->image          = new EmbeddedFile();
        $this->versions       = new ArrayCollection();
        $this->groups         = new ArrayCollection();
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

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): void
    {
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): void
    {
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
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

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullname(): ?string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLastLogin(): ?DateTimeInterface
    {
        return $this->lastLogin;
    }

    public function setLastLogin(?DateTimeInterface $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getIsExternal(): ?bool
    {
        return $this->isExternal;
    }

    public function removeVersion(LogPolicy $version): self
    {
        if ($this->versions->removeElement($version)) {
            // set the owning side to null (unless already changed)
            if ($version->getUser() === $this) {
                $version->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int,Group>
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    public function addGroup(Group $group): self
    {
        if (! $this->groups->contains($group)) {
            $this->groups[] = $group;
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        $this->groups->removeElement($group);

        return $this;
    }
}
