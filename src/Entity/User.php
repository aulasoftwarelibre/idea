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

#[ORM\Table(name: 'fos_user')]
#[ORM\Entity]
#[ORM\Index(columns: ['username'])]
#[ORM\Index(columns: ['email'])]
#[Vich\Uploadable]
#[Validator\Alias]
#[Gedmo\SoftDeleteable]
class User implements EquatableInterface, UserInterface
{
    public const STUDENT  = 'student';
    public const STAFF    = 'staff';
    public const TEACHER  = 'teacher';
    public const EXTERNAL = 'external';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'boolean')]
    protected bool $isExternal;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 180)]
    private string|null $username = null;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 180)]
    #[Assert\Email]
    private string|null $email = null;

    /** @var string[] */
    #[ORM\Column(type: 'array')]
    #[Assert\Choice(['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'], multiple: true)]
    private array $roles = [];

    #[ORM\Column(type: 'string', length: 32)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 16)]
    #[Assert\Regex('/[\w\d_]/u', message: 'form.label_alias_invalid')]
    protected string|null $alias = '';

    #[ORM\Version]
    #[ORM\Column(type: 'integer')]
    private int $version = 1;

    #[ORM\Column(length: 32, nullable: true)]
    #[Assert\Choice(callback: 'getCollectives')]
    #[Assert\NotBlank]
    private string|null $collective = null;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\Degree')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private Degree|null $degree = null;

    #[ORM\Column(length: 4, nullable: true)]
    #[Assert\Regex('/\d{4}/')]
    private string|null $year = null;

    #[ORM\Column(length: 32, unique: false, nullable: true)]
    private string|null $nic = null;

    /** @var Collection<int,Participation> */
    #[ORM\OneToMany(targetEntity: 'App\Entity\Participation', mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $participations;

    /** @var Collection<int,Idea> */
    #[ORM\OneToMany(targetEntity: 'App\Entity\Idea', mappedBy: 'owner', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $ideas;

    /** @var Collection<int,Vote> */
    #[ORM\OneToMany(targetEntity: 'App\Entity\Vote', mappedBy: 'user', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $votes;

    /** @var Collection<int,LogPolicy> */
    #[ORM\OneToMany(targetEntity: 'App\Entity\LogPolicy', mappedBy: 'user')]
    private Collection $versions;

    #[Vich\UploadableField(mapping: 'avatars', fileNameProperty: 'image.name', size: 'image.size', mimeType: 'image.mimeType', originalName: 'image.originalName')]
    private File|UploadedFile|null $imageFile = null;

    #[ORM\Embedded(class: 'Vich\UploaderBundle\Entity\File')]
    private EmbeddedFile $image;

    /** @var Collection<int, Group> */
    #[ORM\ManyToMany(targetEntity: Group::class, inversedBy: 'users', cascade: ['all'], fetch: 'EAGER')]
    #[ORM\JoinTable(name: 'fos_user_user_group')]
    private Collection $groups;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTimeInterface|null $deletedAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $enabled = true;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private string|null $firstname;

    #[ORM\Column(type: 'string', length: 64, nullable: true)]
    private string|null $lastname;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private string|null $biography;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime|null $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    private DateTime|null $updatedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private DateTime|null $lastLogin;

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
            $this->getUsername(),
        );
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->username;
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

    public function getEmail(): string|null
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /** @return string[] */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param string[] $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
    }

    /** @see UserInterface */
    public function getPassword(): void
    {
    }

    /** @see UserInterface */
    public function getSalt(): void
    {
    }

    /** @see UserInterface */
    public function eraseCredentials(): void
    {
    }

    public function equalsTo(self|null $user): bool
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

    public function setAlias(string|null $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /** @return array<string, string> */
    public static function getCollectives(): array
    {
        return [
            'Estudiante' => self::STUDENT,
            'PDI' => self::TEACHER,
            'PAS' => self::STAFF,
            'Otros' => self::EXTERNAL,
        ];
    }

    public function getCollective(): string|null
    {
        return $this->collective;
    }

    public function setCollective(string|null $collective): self
    {
        $this->collective = $collective;

        return $this;
    }

    /** @return Collection<int,Idea> */
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

    /** @return Collection<int,Vote> */
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

    /** @return Collection<int,LogPolicy> */
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

    public function getDegree(): Degree|null
    {
        return $this->degree;
    }

    public function setDegree(Degree|null $degree): self
    {
        $this->degree = $degree;

        return $this;
    }

    public function getYear(): string|null
    {
        return $this->year;
    }

    public function setYear(string|null $year): self
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
    public function setImageFile(File|null $image = null): void
    {
        $this->imageFile = $image;

        if (! $image) {
            return;
        }

        // It is required that at least one field changes if you are using doctrine
        // otherwise the event listeners won't be called and the file is lost
        $this->updatedAt = new DateTime();
    }

    public function getImageFile(): File|null
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

    public function getNic(): string|null
    {
        return $this->nic;
    }

    public function setNic(string|null $nic): self
    {
        $this->nic = $nic;

        return $this;
    }

    /** @return Collection<int,Participation> */
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

    public function getDeletedAt(): DateTimeInterface|null
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(DateTimeInterface|null $deletedAt): void
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

    public function getEnabled(): bool|null
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFirstname(): string|null
    {
        return $this->firstname;
    }

    public function setFirstname(string|null $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string|null
    {
        return $this->lastname;
    }

    public function setLastname(string|null $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullname(): string|null
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    public function getBiography(): string|null
    {
        return $this->biography;
    }

    public function setBiography(string $biography): self
    {
        $this->biography = $biography;

        return $this;
    }

    public function getCreatedAt(): DateTimeInterface|null
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTimeInterface|null
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface|null $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getLastLogin(): DateTimeInterface|null
    {
        return $this->lastLogin;
    }

    public function setLastLogin(DateTimeInterface|null $lastLogin): self
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    public function getIsExternal(): bool|null
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

    /** @return Collection<int,Group> */
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
