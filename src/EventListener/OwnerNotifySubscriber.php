<?php

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\EventListener;

use App\Entity\Idea;
use App\Entity\TelegramChat;
use App\Entity\User;
use App\Event\IdeaWasApprovedEvent;
use App\Event\IdeaWasVotedEvent;
use App\Messenger\TelegramChat\SendMessageToTelegramUserChatCommand;
use App\Repository\IdeaRepository;
use FOS\CommentBundle\Event\CommentEvent;
use FOS\CommentBundle\Events;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Templating\EngineInterface;

class OwnerNotifySubscriber implements EventSubscriberInterface
{
    /**
     * @var MessageBusInterface
     */
    private $bus;
    /**
     * @var EngineInterface
     */
    private $engine;
    /**
     * @var IdeaRepository
     */
    private $ideaRepository;

    public function __construct(
        MessageBusInterface $bus,
        EngineInterface $engine,
        IdeaRepository $ideaRepository
    ) {
        $this->bus = $bus;
        $this->engine = $engine;
        $this->ideaRepository = $ideaRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            IdeaWasApprovedEvent::class => 'ideaWasApproved',
            IdeaWasVotedEvent::class => 'ideaWasVoted',
            Events::COMMENT_POST_PERSIST => 'commentWasDone',
        ];
    }

    public function commentWasDone(CommentEvent $event)
    {
        $comment = $event->getComment();
        $ideaId = $comment->getThread()->getId();
        $commenter = $comment->getAuthorName();
        if ($commenter instanceof User) {
            $commenter = $commenter->getUsername();
        }

        $idea = $this->ideaRepository->find($ideaId);
        if (!$idea instanceof Idea) {
            return;
        }

        $owner = $idea->getOwner();
        $telegramChat = $owner->getTelegramChat();
        if (!$telegramChat || !$telegramChat->isEnabledNotification(TelegramChat::NOTIFY_COMMENTS)) {
            return;
        }

        $ideaOwner = $owner->getUsername();
        if ($ideaOwner === $commenter) {
            return;
        }

        $message = $this->engine->render('telegram/owner_idea_message_received.txt.twig', [
            'idea' => $idea,
            'commenter' => $commenter,
        ]);

        $this->bus->dispatch(
            new SendMessageToTelegramUserChatCommand(
                $owner->getTelegramChat()->getId(),
                $message
            )
        );
    }

    public function ideaWasApproved(IdeaWasApprovedEvent $event)
    {
        $idea = $event->getIdea();
        $owner = $idea->getOwner();

        $telegramChat = $owner->getTelegramChat();
        if (!$telegramChat || !$telegramChat->isEnabledNotification(TelegramChat::NOTIFY_VOTES)) {
            return;
        }

        $message = $this->engine->render('telegram/owner_idea_approved.txt.twig', [
            'idea' => $idea,
        ]);

        $this->bus->dispatch(
            new SendMessageToTelegramUserChatCommand(
                $owner->getTelegramChat()->getId(),
                $message
            )
        );
    }

    public function ideaWasVoted(IdeaWasVotedEvent $event)
    {
        $idea = $event->getIdea();
        $owner = $idea->getOwner();
        $voter = $event->getVoter();

        $telegramChat = $owner->getTelegramChat();
        if (!$telegramChat || !$telegramChat->isEnabledNotification(TelegramChat::NOTIFY_VOTES)) {
            return;
        }

        $message = $this->engine->render('telegram/owner_idea_voted.txt.twig', [
            'idea' => $idea,
            'voter' => $voter,
        ]);

        $this->bus->dispatch(
            new SendMessageToTelegramUserChatCommand(
                $owner->getTelegramChat()->getId(),
                $message
            )
        );
    }
}
