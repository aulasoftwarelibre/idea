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

namespace App\Twig\Extension;

use App\Entity\Idea;
use DateTimeImmutable;
use Spatie\CalendarLinks\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IdeaExtension extends AbstractExtension
{
    public function __construct(
        private RouterInterface $router,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('calendar_url', [$this, 'calendarUrl']),
        ];
    }

    public function calendarUrl(Idea $idea): string
    {
        $address = $idea->getLocation();
        if ($idea->isOnline()) {
            $address = $this->router->generate('idea_jitsi', ['slug' => $idea->getSlug()], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        return (new Link(
            (string) $idea->getTitle(),
            $idea->getStartsAt() ?? new DateTimeImmutable(),
            $idea->getEndsAt() ?? new DateTimeImmutable(),
        ))
            ->address((string) $address)
            ->google();
    }
}
