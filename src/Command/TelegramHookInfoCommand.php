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

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Telegram\Bot\Api;
use Telegram\Bot\TelegramRequest;

class TelegramHookInfoCommand extends Command
{
    /**
     * @var Api
     */
    private $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('idea:telegram:info');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);

        $id = $this->telegram->getMe();

        $io->writeln("El id del bot es: {$id->getId()}");
        $io->writeln("El nick del bot es: {$id->getUsername()}");
        $io->writeln("El webhook del bot es: {$this->getWebhookInfo()}");
    }

    protected function getWebhookInfo(): string
    {
        $request = new TelegramRequest(
            $this->telegram->getAccessToken(),
            'GET',
            'getWebhookInfo'
        );

        return $this->telegram->getClient()->sendRequest($request)->getBody();
    }
}
