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

namespace App\Messenger\TelegramChat;

use App\Entity\TelegramChat;
use App\Repository\TelegramChatRepository;
use BotMan\BotMan\BotMan;
use Sgomez\Bundle\BotmanBundle\Model\Telegram\User;
use Tightenco\Collect\Support\Collection;

class SendWelcomeMessageHandler
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

        /** @var null|TelegramChat $telegramChat */
        $telegramChat = $this->repository->find($chatId);
        if (null === $telegramChat || null === $telegramChat->getWelcomeMessage() || false === $telegramChat->isActive()) {
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
