<?php

/*
 * This file is part of the `idea` project.
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
use Ramsey\Uuid\Uuid;
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
 * @Vich\Uploadable()
 */
class User extends BaseUser implements EquatableInterface
{
    const STUDENT = 'student';
    const STAFF = 'staff';
    const TEACHER = 'teacher';
    const EXTERNAL = 'external';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(name="ssp_id", type="string", length=50, unique=true, nullable=true)
     */
    protected $ssp_id;

    /**
     * @var string
     */
    protected $sspAccessToken;

    /**
     * @var TelegramChat|null
     * @ORM\OneToOne(targetEntity="TelegramChat", inversedBy="user")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    protected $telegramChat;

    /**
     * @var string|null
     * @ORM\Column(length=100, nullable=true)
     */
    protected $telegramSecretToken;

    /**
     * @var \DateTime|null
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $telegramSecretTokenExpiresAt;

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
     * @var Participation[]
     * @ORM\OneToMany(targetEntity="App\Entity\Participation", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $participations;

    /**
     * @var Idea[]
     * @ORM\OneToMany(targetEntity="App\Entity\Idea", mappedBy="owner", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $ideas;

    /**
     * @var Vote[]
     * @ORM\OneToMany(targetEntity="App\Entity\Vote", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $votes;

    /**
     * @var File
     * @Vich\UploadableField(mapping="avatars", fileNameProperty="image.name", size="image.size", mimeType="image.mimeType", originalName="image.originalName")
     */
    private $imageFile;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File")
     *
     * @var string
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

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
     *
     * @return bool
     */
    public function equalsTo(self $user)
    {
        return $this->getId() === $user->getId();
    }

    /**
     * @return string
     */
    public function getSspId()
    {
        return $this->ssp_id;
    }

    /**
     * @param string $ssp_id
     *
     * @return User
     */
    public function setSspId($ssp_id)
    {
        $this->ssp_id = $ssp_id;
        $this->username = $ssp_id;

        return $this;
    }

    /**
     * @return string
     */
    public function getSspAccessToken()
    {
        return $this->sspAccessToken;
    }

    /**
     * @param string $sspAccessToken
     *
     * @return User
     */
    public function setSspAccessToken($sspAccessToken)
    {
        $this->sspAccessToken = $sspAccessToken;

        return $this;
    }

    /**
     * @return TelegramChat|null
     */
    public function getTelegramChat(): ?TelegramChat
    {
        return $this->telegramChat;
    }

    /**
     * @param TelegramChat|null $telegramChat
     *
     * @return User
     */
    public function setTelegramChat(?TelegramChat $telegramChat): self
    {
        $this->telegramChat = $telegramChat;
        $this->telegramSecretToken = null;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getTelegramSecretToken(): ?string
    {
        return $this->telegramSecretToken;
    }

    /**
     * @return \DateTime|null
     */
    public function getTelegramSecretTokenExpiresAt(): ?\DateTime
    {
        return $this->telegramSecretTokenExpiresAt;
    }

    /**
     * Create new token.
     */
    public function generateNewSecretToken(): self
    {
        $this->telegramSecretToken = trim(base64_encode(Uuid::uuid4()->toString()), '=');
        $this->telegramSecretTokenExpiresAt = new \DateTime('+10 minutes'); // 10 minutes

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
     * @param string|null $collective
     *
     * @return User
     */
    public function setCollective(?string $collective): self
    {
        $this->collective = $collective;

        return $this;
    }

    /**
     * @return Idea[]
     */
    public function getIdeas(): array
    {
        return $this->ideas;
    }

    /**
     * @param Idea $idea
     *
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
    public function removeIdea(Idea $idea)
    {
        $this->ideas->removeElement($idea);
    }

    /**
     * @return Vote[]
     */
    public function getVotes(): array
    {
        return $this->votes;
    }

    /**
     * @param Vote $vote
     *
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
    public function removeVote(Vote $vote)
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
     * @param Degree|null $degree
     *
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
     * @param string|null $year
     *
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
     * @param File|UploadedFile $image
     */
    public function setImageFile(File $image = null)
    {
        $this->imageFile = $image;

        if ($image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
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
     * @param EmbeddedFile $image
     *
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
     * @param string|null $nic
     *
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
     * @param Participation $participation
     *
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
    public function removeParticipation(Participation $participation)
    {
        $this->participations->removeElement($participation);
    }

    public function isEqualTo(UserInterface $user)
    {
        return $user instanceof self
            && $user->getId() === $this->getId();
    }
}
