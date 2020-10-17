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

namespace App\MessageHandler\Email;

use App\Entity\Idea;
use App\Entity\User;
use App\Entity\Vote;
use App\Message\Email\SendEmailCommand;
use App\Repository\IdeaRepository;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use function implode;
use function sprintf;

class SendEmailCommandHandler
{
    private IdeaRepository $ideaRepository;
    private LoggerInterface $logger;
    private MailerInterface $mailer;
    private string $assetsPath;
    private string $mailFrom;
    private TokenStorageInterface $token;

    public function __construct(
        IdeaRepository $ideaRepository,
        LoggerInterface $logger,
        MailerInterface $mailer,
        TokenStorageInterface $token,
        string $assetsPath,
        string $mailFrom
    ) {
        $this->ideaRepository = $ideaRepository;
        $this->logger         = $logger;
        $this->mailer         = $mailer;
        $this->token          = $token;
        $this->assetsPath     = $assetsPath;
        $this->mailFrom       = $mailFrom;
    }

    public function __invoke(SendEmailCommand $command): void
    {
        $ideaId  = $command->getIdeaId();
        $message = $command->getMessage();
        $isTest  = $command->isTest();

        $idea = $this->ideaRepository->find($ideaId);

        if (! $idea instanceof Idea) {
            throw new InvalidArgumentException(sprintf('Idea %s not found', $ideaId));
        }

        $token = $this->token->getToken();
        if (! $token instanceof TokenInterface) {
            return;
        }

        $user = $token->getUser();
        if (! $user instanceof User) {
            return;
        }

        $email = $this->createEmail($idea, $message, $isTest, $user);
        $this->mailer->send($email);
    }

    private function createEmail(Idea $idea, string $message, bool $isTest, User $user): TemplatedEmail
    {
        $email = (new TemplatedEmail())
            ->from($this->mailFrom)
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
            $loggedUserEmail = (string) $user->getEmail();
            $email->to($loggedUserEmail);
            $this->logger->debug('[MAIL TO] Enviada prueba');

            return $email;
        }

        $toUsers = $idea->getVotes()->map(static function (Vote $vote) {
            return $vote->getUser()->getEmail();
        })->toArray();

        $email->to($this->mailFrom);
        $email->bcc(...$toUsers);
        $this->logger->debug('[MAIL TO] Destinatarios: ' . implode(', ', $toUsers));

        return $email;
    }
}
