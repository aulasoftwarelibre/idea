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

use App\MessageBus\CommandBus;
use App\Messenger\TelegramChat\SendMessageToTelegramChatsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TelegramSendCommand extends Command
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('idea:telegram:send')
            ->setDescription('Enviar un mensaje a todos los grupos')
            ->addArgument('msg', InputArgument::OPTIONAL, 'Argument description');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('msg');

        if (empty($message) || !\is_string($message)) {
            $io->error('El mensaje está vacío o no es válido');

            return;
        }

        $this->commandBus->dispatch(
            new SendMessageToTelegramChatsCommand(
                $message
            )
        );

        $io->success('Mensaje enviado.');
    }
}
