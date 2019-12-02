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

namespace App\Messenger\User;

use App\Entity\User;
use App\Exception\UserNotFoundException;
use App\MessageBus\CommandHandlerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class RemoveUserHandler implements CommandHandlerInterface
{
    /**
     * @var UserManagerInterface
     */
    private $userManager;

    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    public function __invoke(RemoveUserCommand $command): void
    {
        $user = $this->userManager->findUserByUsername($command->getUsername());
        if (!$user instanceof User) {
            throw new UserNotFoundException('usuario no encontrado');
        }

        if ($command->isHardDelete()) {
            $user->setDeletedAt(new \DateTime());
        }
        $this->userManager->deleteUser($user);
    }
}
