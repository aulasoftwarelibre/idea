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

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipationRepository")
 * @ORM\Table()
 */
class Participation
{
    public const ATTENDEE  = 'attendee';
    public const PRESENTER = 'presenter';
    public const ORGANIZER = 'organizer';

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue("AUTO")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(length=16)
     *
     * @Assert\Choice(callback="getRoles")
     */
    private string $role = self::ATTENDEE;

    /** @ORM\Column(type="boolean") */
    private bool $isReported = false;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private DateTime $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    private DateTime $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="participations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Activity", inversedBy="participations")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Activity $activity;

    public function getId(): ?int
    {
        return $this->id;
    }

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
     * @return array<string, string>
     */
    public static function getRoles(): array
    {
        return [
            'Asistente' => self::ATTENDEE,
            'Organizador' => self::ORGANIZER,
            'Ponente' => self::PRESENTER,
        ];
    }

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

    public function getTitle(): string
    {
        return $this->getActivity()->getTitle();
    }

    public function getDuration(): ?int
    {
        return $this->getActivity()->getDuration();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }
}
