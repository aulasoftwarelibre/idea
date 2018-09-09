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

/**
 * @ORM\Entity(repositoryClass="App\Repository\TelegramChatGroupRepository")
 */
class TelegramChatGroup extends TelegramChat
{
    /**
     * @return null|string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return TelegramChatGroup
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getWelcomeMessage(): ?string
    {
        return $this->welcomeMessage;
    }

    /**
     * @return TelegramChatGroup
     */
    public function setWelcomeMessage(?string $welcomeMessage): self
    {
        $this->welcomeMessage = $welcomeMessage;

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
     * @return TelegramChatGroup
     */
    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }
}
