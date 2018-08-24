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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramHookSetCommand extends Command
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

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('idea:telegram:set')
            ->addOption('certificate', 'c', InputOption::VALUE_OPTIONAL);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $certificate = $input->getOption('certificate');
        if ($certificate && false === file_exists($certificate)) {
            $io->error("El certificado no existe: {$certificate}");

            return 1;
        }

        $id = $this->telegram->getMe();
        $hookRoute = $this->router->generate('telegram_hook', [], RouterInterface::ABSOLUTE_URL);

        $output->writeln("El id del bot es: {$id->getId()}");
        $output->writeln("El hook del bot es: {$hookRoute}");

        try {
            $data = [];
            $data['url'] = $hookRoute;
            if ($certificate) {
                $data['certificate'] = $certificate;
            }
            $result = $this->telegram->setWebhook($data);

            $output->writeln((string) $result->getBody());
        } catch (TelegramSDKException $exception) {
            $output->writeln($exception->getMessage());
        }
    }
}
