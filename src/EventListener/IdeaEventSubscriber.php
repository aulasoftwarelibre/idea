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

use App\Command\SendMessageToTelegramChatsCommand;
use App\Event\IdeaWasCreatedEvent;
use League\Tactician\CommandBus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Templating\EngineInterface;

class IdeaEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var EngineInterface
     */
    private $engine;

    public function __construct(CommandBus $bus, EngineInterface $engine)
    {
        $this->bus = $bus;
        $this->engine = $engine;
    }

    public static function getSubscribedEvents()
    {
        return [
            IdeaWasCreatedEvent::class => 'ideaWasCreated',
        ];
    }

    public function ideaWasCreated(IdeaWasCreatedEvent $event)
    {
        $idea = $event->getIdea();

        $message = $this->engine->render('telegram/notify_new_idea_was_created.txt.twig', [
            'idea' => $idea,
        ]);

        $this->bus->handle(
            new SendMessageToTelegramChatsCommand(
                $message
            )
        );
    }
}
