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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile/telegram/status", name="profile_telegram_status", options={"expose" = true}, methods={"GET"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class StatusTelegramProfileRestController extends AbstractController
{
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getTelegramChat() instanceof TelegramChat) {
            return new JsonResponse([
                'active' => false,
            ]);
        }

        return new JsonResponse([
            'active' => true,
            'username' => $user->getTelegramChat()->getUsername(),
        ]);
    }
}
