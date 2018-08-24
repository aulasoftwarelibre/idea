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

use App\Entity\Idea;
use App\Entity\Vote;
use App\Repository\IdeaRepository;
use FOS\CommentBundle\Event\CommentEvent;
use FOS\CommentBundle\Events;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Templating\EngineInterface;

final class VotersNotifyEventSubscriber implements EventSubscriberInterface
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var EngineInterface
     */
    private $engine;

    /**
     * @var IdeaRepository
     */
    private $ideaRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $mailFrom;

    public function __construct(
        Swift_Mailer $mailer,
        EngineInterface $engine,
        IdeaRepository $ideaRepository,
        LoggerInterface $logger,
        string $mailFrom
    ) {
        $this->mailer = $mailer;
        $this->engine = $engine;
        $this->ideaRepository = $ideaRepository;
        $this->logger = $logger;
        $this->mailFrom = $mailFrom;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::COMMENT_POST_PERSIST => 'commentWasDone',
        ];
    }

    public function commentWasDone(CommentEvent $event): void
    {
        $comment = $event->getComment();
        if ($comment->getParent()) {
            $this->logger->debug('[MAIL IGNORE] Mensaje enviado como respuesta');

            return;
        }

        $ideaId = $comment->getThread()->getId();
        $idea = $this->ideaRepository->find($ideaId);
        if (!$idea instanceof Idea) {
            $this->logger->debug('[MAIL IGNORE] Idea no existe');

            return;
        }

        $ideaOwnerUsername = $idea->getOwner()->getUsername();
        $commenterUsername = $comment->getAuthorName();
        if ($ideaOwnerUsername !== $commenterUsername) {
            $this->logger->debug('[MAIL IGNORE] El que hace el comentario no es el dueño');

            return;
        }

        $toUsers = $idea->getVotes()->map(function (Vote $vote) {
            return $vote->getUser()->getUsername() . '@uco.es';
        })->toArray();

        $this->logger->debug('[MAIL TO] Destinatarios: ' . implode(', ', $toUsers));

        $message = (new Swift_Message('[AulaSL] Comentario de organización'))
            ->setFrom($this->mailFrom)
            ->setTo($this->mailFrom)
            ->setBcc($toUsers)
            ->setBody(
                $this->engine->render('mail/notify_new_author_comment.html.twig', [
                    'idea' => $idea,
                    'comment' => $comment,
                ]),
                'text/html'
            );

        $count = $this->mailer->send($message);

        $this->logger->debug('[MAIL TO] Recibidos por ' . $count);
    }
}
