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

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomepageController
{
    public function __invoke(UrlGeneratorInterface $generator)
    {
        return new RedirectResponse($generator->generate('sonata_admin_dashboard'));
    }
}
