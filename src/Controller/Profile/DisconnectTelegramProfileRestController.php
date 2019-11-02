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

use App\Entity\TelegramChat;
use App\Entity\User;
use App\MessageBus\CommandBus;
use App\Messenger\TelegramChat\UnregisterUserChatCommand;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/telegram/disconnect", name="profile_telegram_disconnect", options={"expose" = true}, methods={"POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class DisconnectTelegramProfileRestController extends AbstractController
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        $telegramChat = $user->getTelegramChat();

        if (!$telegramChat instanceof TelegramChat) {
            return new JsonResponse(['error' => 'Method not allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $this->commandBus->dispatch(
            new UnregisterUserChatCommand(
                $telegramChat->getId()
            )
        );

        $this->addFlash('success', 'Has sido desconectado de Telegram.');

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
