<?php

/*
 * This file is part of the ceo project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Command\GenerateUserTelegramTokenCommand;
use App\Command\UnregisterUserChatCommand;
use App\Entity\Participation;
use App\Entity\TelegramChat;
use App\Entity\User;
use App\Form\Type\ProfileType;
use App\Repository\UserRepository;
use App\Services\Telegram\TelegramCachedCalls;
use League\Tactician\CommandBus;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/profile")
 */
class ProfileController extends Controller
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var CommandBus
     */
    private $bus;
    /**
     * @var TelegramCachedCalls
     */
    private $telegram;

    public function __construct(UserRepository $repository, CommandBus $bus, TelegramCachedCalls $telegram)
    {
        $this->repository = $repository;
        $this->bus = $bus;
        $this->telegram = $telegram;
    }

    /**
     * @Route("/edit", name="profile_edit")
     * @Method({"GET", "POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->get('doctrine.orm.default_entity_manager');

            $manager->persist($user);
            $manager->flush();

            $this->addFlash('positive', 'Su perfil ha sido actualizado');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('/frontend/profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/", name="profile_show")
     * @Method("GET")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function showAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var User $profile */
        $profile = $this->repository->getProfile($user->getId());

        $academicYears = [];
        $activities = 0;
        $hours = 0;

        $profile->getParticipations()->map(function (Participation $participation) use (&$academicYears, &$activities, &$hours) {
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

    /**
     * @Route("/telegram/disconnect", name="profile_telegram_disconnect", options={"expose" = true})
     * @Method("POST")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function disconnectTelegramAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getTelegramChat() instanceof TelegramChat) {
            return new JsonResponse(['error' => 'Method not allowed'], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $this->bus->handle(
            new UnregisterUserChatCommand(
                $user->getTelegramChat()->getId()
            )
        );

        $this->addFlash('success', 'Has sido desconectado de Telegram.');

        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route("/telegram/status", name="profile_telegram_status", options={"expose" = true})
     * @Method("GET")
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function getTelegramStatusAction()
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user->getTelegramChat() instanceof TelegramChat) {
            return new JsonResponse([
                'active' => false,
            ]);
        }

        return new JsonResponse([
            'active' => true,
            'username' => $user->getTelegramChat()->getUsername(),
        ]);
    }

    public function showCard()
    {
        /** @var User $user */
        $user = $this->getUser();
        $profile = $this->repository->getProfile($user->getId());
        $botname = $this->telegram->getMe()->getUsername();

        $token = $this->bus->handle(
            new GenerateUserTelegramTokenCommand(
                $user->getId()
            )
        );

        return $this->render('/frontend/profile/_card.html.twig', [
            'profile' => $profile,
            'token' => $token,
            'botname' => $botname,
        ]);
    }
}
