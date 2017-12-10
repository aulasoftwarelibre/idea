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

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationRepository")
 * @ORM\Table()
 */
class Participation
{
    const ATTENDEE = 'attendee';
    const PRESENTER = 'presenter';
    const ORGANIZER = 'organizer';

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

    /**
     * Participant constructor.
     */
    public function __construct()
    {
        $this->role = static::ATTENDEE;
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->getActivity()->getTitle();
    }

    /**
     * @return int
     */
    public function getDuration(): int
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
     * @param string $role
     *
     * @return Participation
     */
    public function setRole(string $role): Participation
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReported(): bool
    {
        return $this->isReported;
    }

    /**
     * @param bool $isReported
     *
     * @return Participation
     */
    public function setIsReported(bool $isReported): Participation
    {
        $this->isReported = $isReported;

        return $this;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return Participation
     */
    public function setUser(User $user): Participation
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Activity|null
     */
    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    /**
     * @param Activity $activity
     *
     * @return Participation
     */
    public function setActivity(Activity $activity): Participation
    {
        $this->activity = $activity;

        return $this;
    }
}
