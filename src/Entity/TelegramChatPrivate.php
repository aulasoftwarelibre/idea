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
 * @ORM\Entity(repositoryClass="App\Repository\TelegramChatPrivateRepository")
 */
class TelegramChatPrivate extends TelegramChat
{
    /**
     * @return null|string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param null|string $username
     * @return TelegramChatPrivate
     */
    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }


    /**
     * @return null|string
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return TelegramChatPrivate
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @return TelegramChatPrivate
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

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
     * @return TelegramChatPrivate
     */
    public function setUser(?User $user): self
    {
        if ($user) {
            $user->setTelegramChat($this);
        }

        $this->user = $user;

        return $this;
    }

    /**
     * @return array
     */
    public function getNotifications(): array
    {
        return $this->notifications ?? [];
    }

    /**
     * @param string $notification
     *
     * @return bool
     */
    public function isEnabledNotification(string $notification): bool
    {
        return \in_array($notification, $this->notifications, true);
    }

    /**
     * @return TelegramChatPrivate
     */
    public function addNotification(string $notification): self
    {
        if (!\in_array($notification, $this->notifications, true)) {
            $this->notifications[] = $notification;
        }

        return $this;
    }

    /**
     * @param string $notification
     */
    public function removeNotification(string $notification): void
    {
        $this->notifications = array_filter(
            $this->notifications,
            function ($item) use ($notification) {
                return $notification !== $item;
            }
        )
        ;
    }

    /**
     * @return array
     */
    public static function getNotificationsTypes(): array
    {
        return [
            'Comments' => self::NOTIFY_COMMENTS,
            'Votes' => self::NOTIFY_VOTES,
        ];
    }
}
