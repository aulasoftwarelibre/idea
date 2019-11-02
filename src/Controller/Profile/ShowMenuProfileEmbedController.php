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

namespace App\Controller\Profile;

use App\Entity\User;
use App\MessageBus\CommandBus;
use App\Messenger\TelegramChat\GenerateUserTelegramTokenCommand;
use App\Services\Telegram\TelegramCachedCalls;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ShowMenuProfileEmbedController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var TelegramCachedCalls
     */
    private $telegram;

    public function __construct(CommandBus $commandBus, TelegramCachedCalls $telegram)
    {
        $this->commandBus = $commandBus;
        $this->telegram = $telegram;
    }

    public function __invoke(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $botname = $this->telegram->getMe()->getUsername();

        $token = $this->commandBus->dispatch(
            new GenerateUserTelegramTokenCommand(
                (string) $user->getId()
            )
        );

        return $this->render('/frontend/profile/_menu.html.twig', [
            'profile' => $user,
            'token' => $token,
            'botname' => $botname,
        ]);
    }
}
