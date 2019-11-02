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

namespace App\Controller\Profile;

use App\Entity\Participation;
use App\Entity\User;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="profile_show", methods={"GET"})
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class ShowProfileController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var User $profile */
        $profile = $this->userRepository->getProfile($user->getId());

        $academicYears = [];
        $activities = 0;
        $hours = 0;

        $profile->getParticipations()->map(static function (Participation $participation) use (&$academicYears, &$activities, &$hours): void {
            $academicYear = $participation->getActivity()->getAcademicYear();
            $academicYears[$academicYear][] = $participation;

            ++$activities;
            $hours += $participation->getDuration();
        });

        return $this->render('/frontend/profile/show.html.twig', [
            'profile' => $profile,
            'academic_years' => $academicYears,
            'activities' => $activities,
            'hours' => $hours,
        ]);
    }
}
