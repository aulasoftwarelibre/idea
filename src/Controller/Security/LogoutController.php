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

use RuntimeException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/logout", name="logout")
 */
class LogoutController
{
    public function __invoke(): void
    {
        throw new RuntimeException('This method should not be called.');
    }
}
