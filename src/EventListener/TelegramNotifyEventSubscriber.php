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

namespace App\EventListener;

use App\Event\IdeaWasApprovedEvent;
use App\Event\IdeaWasCreatedEvent;
use App\Message\TelegramChat\SendMessageToTelegramChatsCommand;
use App\MessageBus\CommandBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Templating\EngineInterface;

final class TelegramNotifyEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var EngineInterface
     */
    private $engine;

    public function __construct(CommandBus $commandBus, EngineInterface $engine)
    {
        $this->commandBus = $commandBus;
        $this->engine = $engine;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            IdeaWasApprovedEvent::class => 'ideaWasApproved',
            IdeaWasCreatedEvent::class => 'ideaWasCreated',
        ];
    }

    public function ideaWasApproved(IdeaWasApprovedEvent $event): void
    {
        $idea = $event->getIdea();

        if ($idea->isPrivate()) {
            return;
        }

        $message = $this->engine->render('telegram/idea_approved.txt.twig', [
            'idea' => $idea,
        ]);

        $this->commandBus->dispatch(new SendMessageToTelegramChatsCommand($message));
    }

    public function ideaWasCreated(IdeaWasCreatedEvent $event): void
    {
        $idea = $event->getIdea();

        if ($idea->isPrivate()) {
            return;
        }

        $message = $this->engine->render('telegram/idea_new.txt.twig', [
            'idea' => $idea,
        ]);

        $this->commandBus->dispatch(new SendMessageToTelegramChatsCommand($message));
    }
}
