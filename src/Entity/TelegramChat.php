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

    const NOTIFY_VOTES = 'notify.votes';
    const NOTIFY_COMMENTS = 'notify.comments';

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
     * @var User|null
     * @ORM\OneToOne(targetEntity="User", mappedBy="telegramChat")
     */
    protected $user;

    /**
     * @var array
     * @ORM\Column(type="json", nullable=true)
     */
    private $notifications;

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
        $this->notifications = [];
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

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $user->setTelegramChat($this);
        $this->user = $user;
    }

    /**
     * @return array
     */
    public function getNotifications(): array
    {
        return $this->notifications;
    }

    /**
     * @param string $notification
     *
     * @return bool
     */
    public function isEnabledNotification(string $notification): bool
    {
        return in_array($notification, $this->notifications, true);
    }

    /**
     * @param string $notification
     *
     * @return TelegramChat
     */
    public function addNotification(string $notification): self
    {
        if (!in_array($notification, $this->notifications, true)) {
            $this->notifications[] = $notification;
        }

        return $this;
    }

    public function removeNotification(string $notification)
    {
        $this->notifications = array_filter($this->notifications,
            function ($item) use ($notification) {
                return $notification !== $item;
            })
        ;
    }

    /**
     * @return array
     */
    public static function getNotificationsTypes()
    {
        return [
            'Comments' => self::NOTIFY_COMMENTS,
            'Votes' => self::NOTIFY_VOTES,
        ];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle() ?? $this->getUsername();
    }
}
