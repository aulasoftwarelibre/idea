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
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

use function sprintf;

class IdeaUserPurgeCommand extends Command
{
    private CommandBus $commandBus;
    private EntityManagerInterface $manager;
    private UserRepository $userRepository;

    public function __construct(CommandBus $commandBus, EntityManagerInterface $manager, UserRepository $userRepository)
    {
        parent::__construct();

        $this->commandBus     = $commandBus;
        $this->manager        = $manager;
        $this->userRepository = $userRepository;
    }

    protected function configure(): void
    {
        $this
            ->setName('idea:user:purge')
            ->setDescription('Purge all users who has been deleted more than one year ago.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->manager->getFilters()->disable('softdeleteable');
            $users = $this->userRepository->findAllDeletedUsers();

            foreach ($users as $user) {
                $username = $user->getUsername();
                $io->comment(sprintf('Removing %s...', $username));
                $this->commandBus->dispatch(
                    new RemoveUserCommand(
                        $username,
                        true
                    )
                );
            }

            $io->success('All users have been removed.');
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
