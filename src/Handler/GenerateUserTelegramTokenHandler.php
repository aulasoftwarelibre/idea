<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Handler;

use App\Command\GenerateUserTelegramTokenCommand;
use App\Entity\TelegramChat;
use App\Entity\User;
use App\Repository\UserRepository;

class GenerateUserTelegramTokenHandler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * GenerateUserTelegramTokenHandler constructor.
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(GenerateUserTelegramTokenCommand $command)
    {
        $userId = $command->getUserId();

        $user = $this->repository->find($userId);
        if (!$user instanceof User || $user->getTelegramChat() instanceof TelegramChat) {
            return null;
        }

        $user->generateNewSecretToken();
        $this->repository->add($user);

        return $user->getTelegramSecretToken();
    }
}
