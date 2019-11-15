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

namespace App\Messenger\LogPolicy;

use App\Entity\LogPolicy;
use App\MessageBus\CommandHandlerInterface;
use Doctrine\Common\Persistence\ObjectManager;

class UserAcceptedLastPolicyVersionHandler implements CommandHandlerInterface
{
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function __invoke(UserAcceptedLastPolicyVersionCommand $command): void
    {
        $user = $command->getUser();

        $logPolicy = new LogPolicy();

        $logPolicy->setUser($user);
        $logPolicy->setCreateAt(new \DateTime());
        $this->manager->persist($logPolicy);
    }
}
