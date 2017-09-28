<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Console;

use App\Command\CloseIdeaCommand;
use App\Entity\Idea;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CloseIdeaConsole extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('ceo:idea:close')
            ->setDescription('Closes a idea.')
            ->addArgument('ideaId', InputArgument::REQUIRED, 'Idea id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $ideaId = $input->getArgument('ideaId');
        $bus = $this->getContainer()->get('tactician.commandbus.default');

        $user = $this->getContainer()->get('uco.user.provider')
            ->loadUserByUsername('cc0gobas@uco.es');
        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );
        $this->getContainer()->get('security.token_storage')->setToken($token);

        $em = $this->getContainer()->get('doctrine');
        $idea = $em->getRepository(Idea::class)->find($ideaId);

        if (!$idea instanceof Idea) {
            $output->writeln('La idea no existe');

            return 1;
        }

        $bus->handle(
            new CloseIdeaCommand(
                $idea
            )
        );

        $output->writeln('Idea cerrada.');
    }
}
