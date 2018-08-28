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
use Sgomez\Bundle\BotmanBundle\Services\Http\TelegramClient;

class NewChatMembersHandler
{
    /**
     * @var TelegramChatRepository
     */
    private $repository;
    /**
     * @var TelegramClient
     */
    private $client;

    public function __construct(TelegramChatRepository $repository, TelegramClient $client)
    {
        $this->repository = $repository;
        $this->client = $client;
    }

    public function __invoke(NewChatMembersCommand $command): ?TelegramChat
    {
        $message = $command->getMessage();
        $me = $this->client->getMe();

        foreach ($message->getNewChatMembers() ?? [] as $chatMember) {
            if ($chatMember->getId() === $me->getId()) {
                $chat = $message->getChat();
                $telegramChat = $this->repository->find($chat->getId());

                if (!$telegramChat instanceof TelegramChat) {
                    $telegramChat = new TelegramChat((string) $chat->getId(), $chat->getType());
                    $telegramChat->setTitle($chat->getTitle());

                    $this->repository->add($telegramChat);
                }

                return $telegramChat;
            }
        }

        return null;
    }
}
