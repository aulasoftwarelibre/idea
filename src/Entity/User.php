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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Entity\BaseUser;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Class User.
 *
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
 *     )
 * })
 * @Vich\Uploadable()
 * @Validator\Alias()
 */
class User extends BaseUser implements EquatableInterface
{
    public const STUDENT = 'student';
    public const STAFF = 'staff';
    public const TEACHER = 'teacher';
    public const EXTERNAL = 'external';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $isExternal;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $hasProfile = false;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=32)
     * @Assert\NotBlank()
     * @Assert\Length(min=3, max=16)
     * @Assert\Regex("/[\w\d_]/u", message="form.label_alias_invalid")
     */
    protected $alias = '';

    /**
     * @var string|null
     * @ORM\Column(length=32, nullable=true)
     * @Assert\Choice(callback="getCollectives")
     * @Assert\NotBlank()
     */
    private $collective;

    /**
     * @var Degree|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Degree")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $degree;

    /**
     * @var string|null
     * @ORM\Column(length=4, nullable=true)
     * @Assert\Regex("/\d{4}/")
     */
    private $year;

    /**
     * @var string|null
     * @ORM\Column(length=32, unique=true, nullable=true)
     */
    private $nic;

    /**
     * @var Participation[]|Collection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Participation",
     *     mappedBy="user",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $participations;

    /**
     * @var Idea[]|Collection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Idea",
     *     mappedBy="owner",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $ideas;

    /**
     * @var Vote[]|Collection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Vote",
     *     mappedBy="user",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $votes;

    /**
     * @var Comment[]|Collection
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Comment",
     *     mappedBy="author",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $comments;

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

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var EmbeddedFile
     */
    private $image;

    /**
     * @var Group[]|Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    public static function createUcoUser(string $username): self
    {
        $user = new self();
        $user
            ->setUsername($username)
            ->setEmail("{$username}@uco.es")
            ->setPassword('!')
            ->setIsExternal(false)
            ->setEnabled(true)
        ;

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
            ->setCollective(self::EXTERNAL)
        ;

        return $user;
    }

    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->ideas = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->participations = new ArrayCollection();
        $this->image = new EmbeddedFile();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            '%s %s [%s]',
            $this->getFirstname(),
            $this->getLastname(),
            $this->getUsername()
        );
    }

    /**
     * @param User $user
     */
    public function equalsTo(self $user): bool
    {
        return $this->getId() === $user->getId();
    }

    /**
     * @return bool
     */
    public function getHasProfile(): bool
    {
        return $this->hasProfile;
    }

    /**
     * @return User
     */
    public function setHasProfile(bool $hasProfile): self
    {
        $this->hasProfile = $hasProfile;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExternal(): bool
    {
        return $this->isExternal;
    }

    /**
     * @return User
     */
    public function setIsExternal(bool $isExternal): self
    {
        $this->isExternal = $isExternal;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAlias(): ?string
    {
        return $this->alias;
    }

    /**
     * @return User
     */
    public function setAlias(?string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return array
     */
    public static function getCollectives(): array
    {
        return [
            'Estudiante' => static::STUDENT,
            'PDI' => static::TEACHER,
            'PAS' => static::STAFF,
            'Otros' => static::EXTERNAL,
        ];
    }

    /**
     * @return string|null
     */
    public function getCollective(): ?string
    {
        return $this->collective;
    }

    /**
     * @return User
     */
    public function setCollective(?string $collective): self
    {
        $this->collective = $collective;

        return $this;
    }

    /**
     * @return Idea[]|Collection
     */
    public function getIdeas(): Collection
    {
        return $this->ideas;
    }

    /**
     * @return User
     */
    public function addIdea(Idea $idea): self
    {
        $this->ideas[] = $idea;

        return $this;
    }

    /**
     * @param Idea $idea
     */
    public function removeIdea(Idea $idea): void
    {
        $this->ideas->removeElement($idea);
    }

    /**
     * @return Vote[]|Collection
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    /**
     * @return User
     */
    public function addVote(Vote $vote): self
    {
        $vote->setUser($this);
        $this->votes[] = $vote;

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
     * @return Degree|null
     */
    public function getDegree(): ?Degree
    {
        return $this->degree;
    }

    /**
     * @return User
     */
    public function setDegree(?Degree $degree): self
    {
        $this->degree = $degree;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getYear(): ?string
    {
        return $this->year;
    }

    /**
     * @return User
     */
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

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @return User
     */
    public function setImage(EmbeddedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return EmbeddedFile
     */
    public function getImage(): EmbeddedFile
    {
        return $this->image;
    }

    /**
     * @return string|null
     */
    public function getNic(): ?string
    {
        return $this->nic;
    }

    /**
     * @return User
     */
    public function setNic(?string $nic): self
    {
        $this->nic = $nic;

        return $this;
    }

    /**
     * @return Participation[]|Collection
     */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    /**
     * @return User
     */
    public function addParticipation(Participation $participation): self
    {
        $participation->setUser($this);
        $this->participations[] = $participation;

        return $this;
    }

    /**
     * @param Participation $participation
     */
    public function removeParticipation(Participation $participation): void
    {
        $this->participations->removeElement($participation);
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof self
            && $user->getId() === $this->getId();
    }
}
