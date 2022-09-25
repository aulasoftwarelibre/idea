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

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table]
class LogPolicy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private DateTimeInterface $createAt;

    #[ORM\Column(type: 'boolean')]
    private bool $mandatory = true;

    #[ORM\Column(type: 'string', length: 255)]
    private string $version;

    #[ORM\ManyToOne(targetEntity: 'App\Entity\User', inversedBy: 'versions')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User|null $user = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $tag = 'general';

    public function __construct()
    {
    }

    public function getId(): int|null
    {
        return $this->id;
    }

    public function getTag(): string|null
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;

        return $this;
    }

    public function getCreateAt(): DateTimeInterface|null
    {
        return $this->createAt;
    }

    public function setCreateAt(DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getMandatory(): bool|null
    {
        return $this->mandatory;
    }

    public function setMandatory(bool $mandatory): self
    {
        $this->mandatory = $mandatory;

        return $this;
    }

    public function getVersion(): string|null
    {
        return $this->version;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getUser(): User|null
    {
        return $this->user;
    }

    public function setUser(User|null $user): self
    {
        $this->user = $user;

        return $this;
    }
}
