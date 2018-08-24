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

use App\Entity\Activity;
use App\Entity\Participation;
use App\Entity\User;
use App\Repository\ActivityRepository;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CeoReportActivitiesCommand extends Command
{
    /**
     * @var ActivityRepository
     */
    private $activityRepository;
    /**
     * @var Slugify
     */
    private $slugify;

    public function __construct(ActivityRepository $activityRepository)
    {
        parent::__construct();

        $this->activityRepository = $activityRepository;
        $this->slugify = new Slugify();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('idea:repost:activities')
            ->setDescription('Informe de todas las actividades')
            ->addArgument('template', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $io = new SymfonyStyle($input, $output);
        $template = $input->getArgument('template');

        if (false === file_exists($template)) {
            throw new InvalidArgumentException("Plantilla no existe: {$template}");
        }

        $activities = $this->activityRepository->findAll();

        /** @var Activity $activity */
        foreach ($activities as $activity) {
            if ($activity->getParticipations()->isEmpty()) {
                continue;
            }

            $io->comment("Generando actas de {$activity->getTitle()}.");

            $report = IOFactory::load($template);
            $this->generateActivityReport($report, $activity);
        }

        $io->success('Hecho.');
    }

    protected function generateActivityReport(Spreadsheet $report, Activity $activity): void
    {
        /** @var User $organizer */
        $organizer = $activity
            ->getParticipations()
            ->filter(function (Participation $participation) {
                return Participation::ORGANIZER === $participation->getRole();
            })
            ->first()
            ->getUser();

        $students = $activity
            ->getParticipations()
            ->map(function (Participation $participation) {
                return $participation->getUser();
            })
            ->filter(function (User $user) {
                return
                    User::STUDENT === $user->getCollective()
                    && $user->getNic();
            });

        $iterator = $students->getIterator();
        $iterator->uasort(function (User $first, User $second) {
            return $first->getLastname() <=> $second->getLastname();
        });
        $students = new ArrayCollection(iterator_to_array($iterator));

        $sheet = $report->getActiveSheet();

        $sheet->setCellValue('B3', mb_strtoupper($activity->getTitle()));
        $sheet->setCellValue('C6', $activity->getOccurredOn()->format('d/m/Y'));
        $sheet->setCellValue('C7', mb_strtoupper("{$organizer->getFullname()}"));
        $sheet->setCellValue('C8', $activity->getDuration());

        $row = 13;
        /** @var User $student */
        foreach ($students as $student) {
            $sheet->setCellValue("A{$row}", mb_strtoupper($student->getNic()));
            $sheet->setCellValue("B{$row}", mb_strtoupper($student->getLastname()));
            $sheet->setCellValue("C{$row}", mb_strtoupper($student->getFirstname()));
            $sheet->setCellValue("D{$row}", 10);

            $sheet->getStyle("A{$row}:D{$row}")
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THIN);

            ++$row;
        }

        ++$row;

        $sheet->setCellValue("B{$row}", mb_strtoupper("Fdo.: {$organizer->getFullname()}"));

        $slug = $this->slugify->slugify($activity->getTitle());
        $date = $activity->getOccurredOn()->format('Ymd');
        $writer = IOFactory::createWriter($report, 'Xlsx');
        $writer->save("var/report/{$date}-{$slug}.xlsx");
    }
}
