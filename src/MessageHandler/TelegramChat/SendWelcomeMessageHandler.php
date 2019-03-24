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

namespace App\MessageHandler\TelegramChat;

use App\Entity\TelegramChatGroup;
use App\Entity\TelegramChatSuperGroup;
use App\Message\TelegramChat\SendWelcomeMessageCommand;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\TelegramChatRepository;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\User;
use Tightenco\Collect\Support\Collection;

final class SendWelcomeMessageHandler implements CommandHandlerInterface
{
    /**
     * @var BotMan
     */
    private $bot;
    /**
     * @var TelegramChatRepository
     */
    private $repository;
    /**
     * @var \HTMLPurifier
     */
    private $telegramPurifier;

    public function __construct(BotMan $bot, TelegramChatRepository $repository, \HTMLPurifier $telegramPurifier)
    {
        $this->bot = $bot;
        $this->repository = $repository;
        $this->telegramPurifier = $telegramPurifier;
    }

    public function __invoke(SendWelcomeMessageCommand $command): void
    {
        $message = $command->getMessage();
        $chatId = $message->getChat()->getId();

        if ($this->checkAllUsersAreBots($message->getNewChatMembers())) {
            return;
        }

        $telegramChat = $this->repository->find($chatId);
        if (!$telegramChat instanceof TelegramChatGroup && !$telegramChat instanceof TelegramChatSuperGroup) {
            return;
        }

        if (null === $telegramChat->getWelcomeMessage() || false === $telegramChat->isActive()) {
            return;
        }

        $welcomeMessage = $this->telegramPurifier->purify($telegramChat->getWelcomeMessage());
        $this->bot->reply($welcomeMessage, [
            'parse_mode' => 'HTML',
        ]);
    }

    private function checkAllUsersAreBots(?array $members): bool
    {
        return Collection::make($members)->filter(function (User $user) {
            return false === $user->isBot();
        })->isEmpty();
    }
}
