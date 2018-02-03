<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils)
    {
        $exception = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'error' => $exception,
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \RuntimeException('La ruta /logout debe estar activa en el cortafuegos.');
    }
}
