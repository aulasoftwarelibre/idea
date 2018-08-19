<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use App\Event\IdeaWasApprovedEvent;
use App\Event\IdeaWasCreatedEvent;
use App\Messenger\TelegramChat\SendMessageToTelegramChatsCommand;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Templating\EngineInterface;

class TelegramNotifySubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var EngineInterface
     */
    private $engine;

    public function __construct(MessageBusInterface $bus, EngineInterface $engine)
    {
        $this->bus = $bus;
        $this->engine = $engine;
    }

    public static function getSubscribedEvents()
    {
        return [
            IdeaWasApprovedEvent::class => 'ideaWasApproved',
            IdeaWasCreatedEvent::class => 'ideaWasCreated',
        ];
    }

    public function ideaWasApproved(IdeaWasApprovedEvent $event)
    {
        $idea = $event->getIdea();

        if ($idea->isPrivate()) {
            return;
        }

        $message = $this->engine->render('telegram/idea_approved.txt.twig', [
            'idea' => $idea,
        ]);

        $this->bus->dispatch(
            new SendMessageToTelegramChatsCommand(
                $message
            )
        );
    }

    public function ideaWasCreated(IdeaWasCreatedEvent $event)
    {
        $idea = $event->getIdea();

        if ($idea->isPrivate()) {
            return;
        }

        $message = $this->engine->render('telegram/idea_new.txt.twig', [
            'idea' => $idea,
        ]);

        $this->bus->dispatch(
            new SendMessageToTelegramChatsCommand(
                $message
            )
        );
    }
}
