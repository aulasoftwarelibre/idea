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

namespace App\Messenger\IdeaMessage;

use App\Entity\Idea;
use App\Entity\Vote;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\IdeaRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SendIdeaMessageHandler implements CommandHandlerInterface
{
    /**
     * @var IdeaRepository
     */
    private $ideaRepository;
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var MailerInterface
     */
    private $mailer;
    /**
     * @var string
     */
    private $assetsPath;
    /**
     * @var string
     */
    private $mailFrom;
    /**
     * @var TokenStorageInterface
     */
    private $token;

    public function __construct(
        IdeaRepository $ideaRepository,
        LoggerInterface $logger,
        MailerInterface $mailer,
        TokenStorageInterface $token,
        string $assetsPath,
        string $mailFrom
    ) {
        $this->ideaRepository = $ideaRepository;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->token = $token;
        $this->assetsPath = $assetsPath;
        $this->mailFrom = $mailFrom;
    }

    public function __invoke(SendIdeaMessageCommand $command): void
    {
        $ideaId = $command->getIdeaId();
        $message = $command->getMessage();
        $isTest = $command->isTest();

        $idea = $this->ideaRepository->find($ideaId);

        if (!$idea instanceof Idea) {
            throw new \InvalidArgumentException("Idea {$ideaId} not found");
        }

        $email = $this->createEmail($idea, $message, $isTest);
        $this->mailer->send($email);
    }

    /**
     * @param Idea   $idea
     * @param string $message
     * @param bool   $isTest
     *
     * @return TemplatedEmail
     */
    private function createEmail(Idea $idea, string $message, bool $isTest): TemplatedEmail
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
            ->to($this->mailFrom)
            ->subject('[AulaSL] ' . $idea->getTitle())
            ->embedFromPath($this->assetsPath . '/images/logo-horizontal-transparente.png', 'logo')
            ->embedFromPath($this->assetsPath . '/images/icon_facebook.png', 'facebook')
            ->embedFromPath($this->assetsPath . '/images/icon_instagram.png', 'instagram')
            ->embedFromPath($this->assetsPath . '/images/icon_telegram.png', 'telegram')
            ->embedFromPath($this->assetsPath . '/images/icon_twitter.png', 'twitter')
            ->embedFromPath($this->assetsPath . '/images/icon_youtube.png', 'youtube')
            ->htmlTemplate('mail/idea_message.html.twig')
            ->context([
                'idea' => $idea,
                'comment' => $message,
            ]);

        if ($isTest) {
            $loggedUserEmail = $this->token->getToken()->getUsername() . '@uco.es';
            $email->bcc($loggedUserEmail);
            $this->logger->debug('[MAIL TO] Enviada prueba');
        } else {
            $toUsers = $idea->getVotes()->map(static function (Vote $vote) {
                return $vote->getUser()->getUsername() . '@uco.es';
            })->toArray();

            $email->bcc(...$toUsers);
            $this->logger->debug('[MAIL TO] Destinatarios: ' . implode(', ', $toUsers));
        }

        return $email;
    }
}
