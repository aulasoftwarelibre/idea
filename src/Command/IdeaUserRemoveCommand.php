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
use App\Messenger\User\RemoveUserCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class IdeaUserRemoveCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'idea:user:remove';
    /**
     * @var EntityManagerInterface
     */
    private $manager;
    /**
     * @var CommandBus
     */
    private $commandBus;

    public function __construct(EntityManagerInterface $manager, CommandBus $commandBus)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('username', InputArgument::REQUIRED, 'Debe ser un nombre de usuario')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        /** @var string $username */
        $username = $input->getArgument('username');

        if ($username) {
            $io->note(sprintf('You passed an argument: %s', $username));
        }

        try {
            $this->manager->getFilters()->disable('softdeleteable');
            $this->commandBus->dispatch(
                new RemoveUserCommand(
                    $username,
                    true
                )
            );
            $io->success('');
        } catch (HandlerFailedException $e) {
            if (!$e->getPrevious() instanceof \Exception) {
                $io->error($e->getMessage());

                return 1;
            }

            $io->error($e->getPrevious()->getMessage());

            return 2;
        }

        return 0;
    }
}
