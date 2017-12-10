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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Routing\RouterInterface;
use Telegram\Bot\Api;

class TelegramHookUnsetCommand extends Command
{
    /**
     * @var Api
     */
    private $telegram;
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(Api $telegram, RouterInterface $router)
    {
        $this->telegram = $telegram;
        $this->router = $router;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ceo:telegram:unset')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $this->telegram->getMe()->getId();

        $output->writeln("El id del bot es: {$id}");

        $result = $this->telegram->removeWebhook();

        $output->writeln((string) $result->getBody());
    }
}
