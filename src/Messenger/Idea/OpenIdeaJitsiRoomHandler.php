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

namespace App\Messenger\Idea;

use App\MessageBus\CommandHandlerInterface;

final class OpenIdeaJitsiRoomHandler implements CommandHandlerInterface
{
    public function __invoke(OpenIdeaJitsiRoomCommand $command): void
    {
        $idea = $command->getIdea();

        if (!$idea->isOnline()) {
            return;
        }

        $idea->setIsJitsiRoomOpen(true);
    }
}
