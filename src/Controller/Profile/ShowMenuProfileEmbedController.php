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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ShowMenuProfileEmbedController extends AbstractController
{
    public function __invoke(): Response
    {
        $user = $this->getUser();

        return $this->render('/frontend/profile/_menu.html.twig', ['profile' => $user]);
    }
}
