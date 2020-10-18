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
use ArrayIterator;
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

use function assert;
use function file_exists;
use function is_array;
use function iterator_to_array;
use function mb_strtoupper;
use function sprintf;

class ReportActivitiesCommand extends Command
{
    private ActivityRepository $activityRepository;
    private Slugify $slugify;

    public function __construct(ActivityRepository $activityRepository)
    {
        parent::__construct();

        $this->activityRepository = $activityRepository;
        $this->slugify            = new Slugify();
    }

    protected function configure(): void
    {
        $this
            ->setName('idea:report:activities')
            ->setDescription('Informe de todas las actividades')
            ->addArgument('template', InputArgument::REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io       = new SymfonyStyle($input, $output);
        $template = $this->getTemplate($input);

        $activities = $this->activityRepository->findAll();

        foreach ($activities as $activity) {
            assert($activity instanceof Activity);
            if ($activity->getParticipations()->isEmpty()) {
                continue;
            }

            $io->comment(sprintf('Generando actas de %s.', $activity->getTitle()));

            $report = IOFactory::load($template);
            $this->generateActivityReport($report, $activity);
        }

        $io->success('Hecho.');

        return 0;
    }

    protected function generateActivityReport(Spreadsheet $report, Activity $activity): void
    {
        $organizer = $activity
            ->getParticipations()
            ->filter(static function (Participation $participation) {
                return $participation->getRole() === Participation::ORGANIZER;
            })
            ->first()
            ->getUser();
        assert($organizer instanceof User);

        $students = $activity
            ->getParticipations()
            ->map(static function (Participation $participation) {
                return $participation->getUser();
            })
            ->filter(static function (User $user) {
                return $user->getCollective() === User::STUDENT
                    && $user->getNic();
            });

        $iterator = $students->getIterator();
        assert($iterator instanceof ArrayIterator);
        $iterator->uasort(static function (User $first, User $second) {
            return $first->getLastname() <=> $second->getLastname();
        });
        $students = new ArrayCollection(iterator_to_array($iterator));

        $sheet = $report->getActiveSheet();

        $sheet->setCellValue('B3', mb_strtoupper($activity->getTitle()));
        $sheet->setCellValue('C6', $activity->getOccurredOn()->format('d/m/Y'));
        $sheet->setCellValue('C7', mb_strtoupper($organizer->getFullname()));
        $sheet->setCellValue('C8', $activity->getDuration());

        $row = 13;
        foreach ($students as $student) {
            assert($student instanceof User);
            $sheet->setCellValue('A' . $row, mb_strtoupper($student->getNic() ?? ''));
            $sheet->setCellValue('B' . $row, mb_strtoupper($student->getLastname()));
            $sheet->setCellValue('C' . $row, mb_strtoupper($student->getFirstname()));
            $sheet->setCellValue('D' . $row, 10);

            $sheet->getStyle('A' . $row . ':D' . $row)
                ->getBorders()
                ->getBottom()
                ->setBorderStyle(Border::BORDER_THIN);

            ++$row;
        }

        ++$row;

        $sheet->setCellValue('B' . $row, mb_strtoupper('Fdo.: ' . $organizer->getFullname()));

        $slug   = $this->slugify->slugify($activity->getTitle());
        $date   = $activity->getOccurredOn()->format('Ymd');
        $writer = IOFactory::createWriter($report, 'Xlsx');
        $writer->save(sprintf('var/report/%s-%s.xlsx', $date, $slug));
    }

    /**
     * @return mixed|string|string[]|null
     */
    protected function getTemplate(InputInterface $input)
    {
        $template = $input->getArgument('template');
        if (is_array($template)) {
            $template = $template[0];
        }

        if (file_exists((string) $template) === false) {
            throw new InvalidArgumentException(sprintf('Plantilla no existe: %s', $template));
        }

        return $template;
    }
}
