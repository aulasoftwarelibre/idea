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

namespace App\Controller\Security;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @Route("/connect/uco", name="connect_uco_start")
 */
class UcoConnectStartController extends AbstractController
{
    use TargetPathTrait;

    public function __invoke(Request $request, ClientRegistry $clientRegistry): Response
    {
        $this->storeTargetPath($request);

        return $clientRegistry
            ->getClient('uco')
            ->redirect(['openid'], []);
    }

    private function storeTargetPath(Request $request): void
    {
        $targetPath = $request->query->get('_target_path');
        $session    = $request->getSession();

        if (! $targetPath || ! $request->hasSession() || ! $session instanceof SessionInterface) {
            return;
        }

        $this->saveTargetPath($session, 'main', $targetPath);
    }
}
