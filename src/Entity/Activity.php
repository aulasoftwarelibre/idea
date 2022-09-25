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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use FOS\RestBundle\Validator\Constraints\Regex;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table]
#[ORM\Entity]
class Activity
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue('AUTO')]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min: 3, max: 255)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private DateTime $occurredOn;

    /** @Regex("/\d{4}\/\d{4}/") */
    #[ORM\Column(length: 32)]
    #[Assert\NotBlank]
    private string $academicYear;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1)]
    private int $duration;

    #[Gedmo\Slug(fields: ['title'], updatable: false, unique: true)]
    #[ORM\Column(length: 255, unique: true)]
    private string $slug;

    /** @var Participation[]|Collection */
    #[ORM\OneToMany(targetEntity: 'App\Entity\Participation', mappedBy: 'activity', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $participations;

    #[Gedmo\Timestampable(on: 'create')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $createdAt;

    #[Gedmo\Timestampable(on: 'update')]
    #[ORM\Column(type: 'datetime')]
    private DateTime $updatedAt;

    public function __construct(string $title, DateTime $occurredOn, string $academicYear, int $duration)
    {
        $this->title          = $title;
        $this->occurredOn     = $occurredOn;
        $this->academicYear   = $academicYear;
        $this->duration       = $duration;
        $this->participations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getTitle() ?? '';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return Activity */
    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getOccurredOn(): DateTime
    {
        return $this->occurredOn;
    }

    /** @return Activity */
    public function setOccurredOn(DateTime $occurredOn): self
    {
        $this->occurredOn = $occurredOn;

        return $this;
    }

    public function getDuration(): int|null
    {
        return $this->duration;
    }

    /** @return Activity */
    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /** @return Participation[]|Collection */
    public function getParticipations(): Collection
    {
        return $this->participations;
    }

    /** @return Activity */
    public function addParticipation(Participation $participant): self
    {
        $participant->setActivity($this);
        $this->participations[] = $participant;

        return $this;
    }

    public function removeParticipation(Participation $participant): void
    {
        $this->participations->removeElement($participant);
    }

    public function getAcademicYear(): string
    {
        return $this->academicYear;
    }

    /** @return Activity */
    public function setAcademicYear(string $academicYear): self
    {
        $this->academicYear = $academicYear;

        return $this;
    }
}
