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

namespace App\Messenger\TelegramChat;

class GenerateUserTelegramTokenCommand
{
    /**
     * @var string
     */
    private $userId;

    /**
     * GenerateUserTelegramTokenCommand constructor.
     */
    public function __construct(string $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return mixed
     */
    public function getUserId(): string
    {
        return $this->userId;
    }
}
