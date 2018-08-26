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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationRepository")
 * @ORM\Table()
 */
class Participation
{
    public const ATTENDEE = 'attendee';
    public const PRESENTER = 'presenter';
    public const ORGANIZER = 'organizer';

    /**
     * @var int
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue("AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=16)
     * @Assert\Choice(callback="getRoles")
     */
    private $role;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $isReported;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="participations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Activity
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="participations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $activity;

    public function __construct(User $user, Activity $activity, string $role = self::ATTENDEE)
    {
        $this->role = $role;
        $this->user = $user;
        $this->activity = $activity;
        $this->isReported = false;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Participation
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Activity
     */
    public function getActivity(): Activity
    {
        return $this->activity;
    }

    /**
     * @return Participation
     */
    public function setActivity(Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * @return array
     */
    public static function getRoles(): array
    {
        return [
            'Asistente' => static::ATTENDEE,
            'Organizador' => static::ORGANIZER,
            'Ponente' => static::PRESENTER,
        ];
    }

    /**
     * @return string
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * @return Participation
     */
    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function getIsReported(): bool
    {
        return $this->isReported;
    }

    /**
     * @return Participation
     */
    public function setIsReported(bool $isReported): self
    {
        $this->isReported = $isReported;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getActivity()->getTitle();
    }

    /**
     * @return int
     */
    public function getDuration(): ?int
    {
        return $this->getActivity()->getDuration();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }
}
