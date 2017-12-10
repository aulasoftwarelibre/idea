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

/**
 * @ORM\Entity(repositoryClass="App\Repository\TelegramChatRepository")
 */
class TelegramChat
{
    const PRIVATE = 'private';
    const GROUP = 'group';
    const SUPERGROUP = 'supergroup';
    const CHANNEL = 'channel';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(length=64)
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(length=32)
     */
    private $type;

    /**
     * @var string|null
     * @ORM\Column(length=256, nullable=true)
     */
    private $title;

    /**
     * @var string|null
     * @ORM\Column(length=256, nullable=true)
     */
    private $username;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $active = false;

    /**
     * TelegramChat constructor.
     *
     * @param string $id
     * @param string $type
     */
    public function __construct(string $id, string $type)
    {
        $this->id = $id;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param null|string $title
     *
     * @return TelegramChat
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     *
     * @return TelegramChat
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return TelegramChat
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->getTitle() ?? $this->getUsername();
    }
}
