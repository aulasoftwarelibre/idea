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

class LeftChatMemberHandler
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

    public function __invoke(LeftChatMemberCommand $command): void
    {
        $message = $command->getMessage();

        $me = $this->client->getMe();

        if (null === $message->getLeftChatMember() || $message->getLeftChatMember()->getId() !== $me->getId()) {
            return;
        }

        $telegramChat = $this->repository->find($message->getChat()->getId());
        if ($telegramChat instanceof TelegramChat) {
            $this->repository->remove($telegramChat);
        }
    }
}
