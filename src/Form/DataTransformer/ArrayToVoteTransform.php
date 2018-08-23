<?php

/*
 * This file is part of the `idea` project.
 *
 * (c) Aula de Software Libre de la UCO <aulasoftwarelibre@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\DataTransformer;

use App\Entity\Vote;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;

class ArrayToVoteTransform implements DataTransformerInterface
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function transform($votes)
    {
        $users = [];
        if ($votes) {
            /** @var Vote $vote */
            foreach ($votes as $vote) {
                $users[] = $vote->getUser()->getId();
            }
        }

        return $users;
    }

    public function reverseTransform($users)
    {
        $votes = new ArrayCollection();

        if ($users) {
            foreach ($users as $userId) {
                $user = $this->repository->find($userId);

                $vote = new Vote();
                $vote->setUser($user);
                $votes[] = $vote;
            }
        }

        return $votes;
    }
}
