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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TelegramChatRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "private" = "App\Entity\TelegramChatPrivate",
 *     "group" = "App\Entity\TelegramChatGroup",
 *     "supergroup" = "App\Entity\TelegramChatSuperGroup",
 *     "channel" = "App\Entity\TelegramChatChannel",
 * })
 */
abstract class TelegramChat
{
    public const PRIVATE = 'private';
    public const GROUP = 'group';
    public const SUPER_GROUP = 'supergroup';
    public const CHANNEL = 'channel';

    public const NOTIFY_VOTES = 'notify.votes';
    public const NOTIFY_COMMENTS = 'notify.comments';

    /**
     * @var string
     * @ORM\Id
     * @ORM\Column(length=64)
     */
    protected $id;

    /**
     * @var string|null
     * @ORM\Column(length=256, nullable=true)
     */
    protected $title;

    /**
     * @var string|null
     * @ORM\Column(length=256, nullable=true)
     */
    protected $username;

    /**
     * @var null|string
     * @ORM\Column(length=255, nullable=true)
     */
    protected $firstName;

    /**
     * @var null|string
     * @ORM\Column(length=255, nullable=true)
     */
    protected $lastName;

    /**
     * @var User|null
     * @ORM\OneToOne(targetEntity="User", mappedBy="telegramChat")
     */
    protected $user;

    /**
     * @var array|string[]
     *
     * @ORM\Column(type="json", nullable=true)
     */
    protected $notifications = [];

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    protected $active = false;

    /**
     * @var null|string
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(min="10", minMessage="error.welcome.message.too.short")
     */
    protected $welcomeMessage;

    /**
     * TelegramChat constructor.
     */
    public function __construct(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}
