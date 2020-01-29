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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

class IdeaUserPurgeCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'idea:user:purge';
    /**
     * @var CommandBus
     */
    private $commandBus;
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    public function __construct(EntityManagerInterface $manager, CommandBus $commandBus)
    {
        parent::__construct();
        $this->manager = $manager;
        $this->commandBus = $commandBus;
    }

    protected function configure(): void
    {
        $this->setDescription('Purge all users who has been softdeleted');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->manager->getFilters()->disable('softdeleteable');
            $users = $this->findAllSoftDeletedUsers();

            foreach ($users as $user) {
                $username = $user->getUsername();
                $this->commandBus->dispatch(
                    new RemoveUserCommand(
                        $username,
                        true
                    )
                );
            }

            $io->success('You have a new command! Now make it your own! Pass --help to see your options.');
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

    public function findAllSoftDeletedUsers(): array
    {
        $qb = $this->manager->createQuery('SELECT u FROM \App\Entity\User u WHERE u.deletedAt IS NOT   NULL');

        return $qb->getResult();
    }
}
