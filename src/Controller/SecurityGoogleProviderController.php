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

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class SecurityGoogleProviderController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @Route("/connect/google", name="connect_google_start")
     */
    public function connect(Request $request, ClientRegistry $clientRegistry): Response
    {
        if ($targetPath = $request->query->get('_target_path')) {
            $this->saveTargetPath($request->getSession(), 'main', $targetPath);
        }

        return $clientRegistry
            ->getClient('google')
            ->redirect([
                'openid', 'email', 'profile',
            ])
        ;
    }

    /**
     * @Route("/connect/google/check", name="connect_google_check")
     */
    public function check(): void
    {
        throw new RuntimeException('This method should not be called.');
    }
}
