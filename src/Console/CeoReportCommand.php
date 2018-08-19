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

use App\Entity\Participation;
use App\Entity\User;
use App\Repository\UserRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CeoReportCommand extends Command
{
    protected static $defaultName = 'ceo:report';
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
    }

    protected function configure()
    {
        $this
            ->setDescription('Save Report')
            ->addArgument('filename', InputArgument::OPTIONAL, 'Nombre del fichero', 'report')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $filename = $input->getArgument('filename');
        $filename = "var/report/{$filename}.xlsx";

        @mkdir('var/report');
        if (file_exists($filename)) {
            unlink($filename);
        }

        $report = new Spreadsheet();
        $sheet = $report->getActiveSheet();

        $users = $this->userRepository->findBy([], [
            'lastname' => 'ASC',
        ]);

        $row = 0;

        /** @var User $user */
        foreach ($users as $user) {
            if (User::STUDENT !== $user->getCollective()) {
                continue;
            }

            if ($user->getParticipations()->isEmpty()) {
                continue;
            }

            $student = sprintf('%s, %s - %s',
                $user->getLastname(),
                $user->getFirstname(),
                $user->getNic()
            );

            ++$row;
            $sheet->setCellValueByColumnAndRow(1, $row, $student);
            $sheet->mergeCells("A{$row}:C{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);

            $participations = $user->getParticipations();
            $totalHours = 0;

            /** @var Participation $participation */
            foreach ($participations as $participation) {
                $activity = $participation->getActivity();
                $duration = $activity->getDuration();
                $totalHours += $duration;

                ++$row;
                $sheet->setCellValueByColumnAndRow(2, $row, $activity->getTitle());
                $sheet->setCellValueByColumnAndRow(3, $row, $duration);
            }

            ++$row;
            $sheet->mergeCells("A{$row}:B{$row}");
            $sheet->getStyle("A{$row}")->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ]);
            $sheet->setCellValue("A{$row}", 'Total horas:');
            $sheet->setCellValue("C{$row}", $totalHours);
        }

        $write = IOFactory::createWriter($report, 'Xlsx');
        $write->save($filename);

        $io->success('Hecho.');
    }
}
