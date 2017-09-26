<?php
/**
 * This file is part of the ceo.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController
{
    public function login(AuthenticationUtils $authenticationUtils, TwigEngine $engine)
    {
        $exception = $authenticationUtils->getLastAuthenticationError();

        return $engine->renderResponse('/security/login.html.twig', [
            'error' => $exception,
        ]);
    }

    public function logout()
    {
        throw new \RuntimeException('La ruta /logout debe estar activa en el cortafuegos.');
    }
}
