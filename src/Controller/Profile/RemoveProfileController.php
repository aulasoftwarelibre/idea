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
use App\Message\User\RemoveUserCommand;
use App\MessageBus\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use function assert;

/**
 * @Route("/profile/remove", name="profile_remove", methods={"POST"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class RemoveProfileController extends AbstractController
{
    public function __invoke(
        CommandBus $commandBus,
        Request $request,
        TokenStorageInterface $tokenStorage
    ): Response {
        if (! $this->isCsrfTokenValid('delete', $request->request->get('token'))) {
            $this->addFlash('error', 'Token CSRF inválido');

            return $this->redirectToRoute('profile_show');
        }

        $user = $this->getUser();
        assert($user instanceof User);

        $commandBus->dispatch(
            new RemoveUserCommand(
                $user->getUsername(),
                false
            )
        );

        $tokenStorage->setToken(null);
        $this->addFlash('success', 'Su usuario ha sido eliminado correctamente');

        return $this->redirectToRoute('homepage');
    }
}
