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

use App\Entity\Participation;
use App\Entity\User;
use App\Repository\UserRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function assert;
use function file_exists;
use function is_array;
use function is_dir;
use function mkdir;
use function sprintf;
use function unlink;

#[AsCommand(
    name: 'idea:report:students',
    description: 'Save Report',
)]
class ReportCommand extends Command
{
    public function __construct(private UserRepository $userRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('filename', InputArgument::OPTIONAL, 'Nombre del fichero', 'report');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io       = new SymfonyStyle($input, $output);
        $filename = $this->getFilename($input);

        $this->warmUp($filename);

        $report = new Spreadsheet();
        $sheet  = $report->getActiveSheet();

        $users = $this->userRepository->findBy([], ['lastname' => 'ASC']);

        $row = 0;

        foreach ($users as $user) {
            assert($user instanceof User);
            if (
                $user->getCollective() !== User::STUDENT || $user->getParticipations()->isEmpty()
            ) {
                continue;
            }

            $student = sprintf(
                '%s, %s - %s',
                $user->getLastname(),
                $user->getFirstname(),
                $user->getNic(),
            );

            ++$row;
            $sheet->setCellValueByColumnAndRow(1, $row, $student);
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);

            $participations = $user->getParticipations();
            $totalHours     = 0;

            foreach ($participations as $participation) {
                assert($participation instanceof Participation);
                $activity    = $participation->getActivity();
                $duration    = $activity->getDuration();
                $totalHours += $duration;

                ++$row;
                $sheet->setCellValueByColumnAndRow(2, $row, $activity->getTitle());
                $sheet->setCellValueByColumnAndRow(3, $row, $duration);
            }

            ++$row;
            $sheet->mergeCells('A' . $row . ':B' . $row);
            $sheet->getStyle('A' . $row)->getAlignment()->applyFromArray([
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
            ]);
            $sheet->setCellValue('A' . $row, 'Total horas:');
            $sheet->setCellValue('C' . $row, $totalHours);
        }

        $write = IOFactory::createWriter($report, 'Xlsx');
        $write->save($filename);

        $io->success('Hecho.');

        return 0;
    }

    protected function warmUp(string $filename): void
    {
        if (! mkdir('var/report') && ! is_dir('var/report')) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', 'var/report'));
        }

        if (! file_exists($filename)) {
            return;
        }

        unlink($filename);
    }

    protected function getFilename(InputInterface $input): mixed
    {
        $filename = $input->getArgument('filename');
        if (is_array($filename)) {
            $filename = $filename[0];
        }

        return sprintf('var/report/%s.xlsx', $filename);
    }
}
