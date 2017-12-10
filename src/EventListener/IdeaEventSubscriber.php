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
use Symfony\Component\Routing\RouterInterface;

class IdeaEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(CommandBus $bus, RouterInterface $router)
    {
        $this->bus = $bus;
        $this->router = $router;
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
        $route = $this->router->generate('idea_show', ['slug' => $idea->getSlug()], RouterInterface::ABSOLUTE_URL);

        $message = <<< EOF
Se acaba de registrar una nueva idea en el portal del Aula.
Título: {$idea->getTitle()}
Más información: {$route}
EOF;

        $this->bus->handle(
            new SendMessageToTelegramChatsCommand(
                $message
            )
        );
    }
}
