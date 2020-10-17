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
use App\Entity\Thread;
use App\Repository\ThreadRepository;
use DateTimeImmutable;
use Spatie\CalendarLinks\Link;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IdeaExtension extends AbstractExtension
{
    private ThreadRepository $threadRepository;
    private TranslatorInterface $translator;
    private RouterInterface $router;

    public function __construct(
        ThreadRepository $threadRepository,
        TranslatorInterface $translator,
        RouterInterface $router
    ) {
        $this->threadRepository = $threadRepository;
        $this->translator       = $translator;
        $this->router           = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('idea_count_comments', [$this, 'getIdeaCountComments']),
            new TwigFunction('calendar_url', [$this, 'calendarUrl']),
        ];
    }

    public function getIdeaCountComments(Idea $idea): string
    {
        $ideaId = $idea->getId();
        $thread = $this->threadRepository->find($ideaId);

        $count = ! $thread instanceof Thread ? 0 : $thread->getNumComments();

        return $this->translator->trans('idea_num_comments', ['%count%' => $count]);
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
            $idea->getEndsAt() ?? new DateTimeImmutable()
        ))
            ->address((string) $address)
            ->google();
    }
}
