<?php


namespace App\Messenger\LogPolicy;


use App\Entity\LogPolicy;
use App\MessageBus\CommandHandlerInterface;
use App\Repository\LogPolicyRepository;
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

    public function __invoke(UserAcceptedLastPolicyVersionCommand $command)
    {
        $user = $command->getUser();

        $logPolicy = new LogPolicy();

        $logPolicy->setUser($user);
        $logPolicy->setCreateAt(new \DateTime());
        $this->manager->persist($logPolicy);
    }
}