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

use App\Message\User\RemoveUserCommand;
use App\MessageBus\CommandBus;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

use function assert;
use function is_string;

#[AsCommand(
    name: 'idea:user:remove',
    description: 'Remove and purge an user by passing username.',
)]
class IdeaUserRemoveCommand extends Command
{
    public function __construct(private CommandBus $commandBus, private EntityManagerInterface $manager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'Username to search');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        assert(is_string($username));

        try {
            $this->manager->getFilters()->disable('softdeleteable');

            $this->commandBus->dispatch(
                new RemoveUserCommand(
                    $username,
                    true,
                ),
            );
            $io->success('User was removed and purged.');
        } catch (HandlerFailedException $e) {
            if (! $e->getPrevious() instanceof Exception) {
                $io->error($e->getMessage());

                return 1;
            }

            $io->error($e->getPrevious()->getMessage());

            return 2;
        } finally {
            $this->manager->getFilters()->enable('softdeleteable');
        }

        return 0;
    }
}
