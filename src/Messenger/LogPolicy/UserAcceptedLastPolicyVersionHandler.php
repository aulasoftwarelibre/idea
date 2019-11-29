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

    /**
     * @var string
     */
    private $policyVersion;

    public function __construct(ObjectManager $manager, string $policyVersion)
    {
        $this->manager = $manager;
        $this->policyVersion = $policyVersion;
    }

    public function __invoke(UserAcceptedLastPolicyVersionCommand $command): void
    {
        $user = $command->getUser();
        $logPolicy = new LogPolicy();
        $logPolicy->setVersion($this->policyVersion);
        $logPolicy->setCreateAt(new \DateTime());

        $user->addVersion($logPolicy);
        $this->manager->persist($logPolicy);
    }
}
