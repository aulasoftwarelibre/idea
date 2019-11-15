<?php


namespace App\Messenger\LogPolicy;

use App\Entity\LogPolicy;
use App\MessageBus\QueryHandlerInterface;

class CheckUserAcceptLastPolicyVersionHandler implements QueryHandlerInterface
{
    /**
     * @var string
     */
    private $policyVersion;

    public function __construct(string $policyVersion)
    {
        $this->policyVersion = $policyVersion;
    }

    public function __invoke(CheckUserAccpetLastPolicyVersionQuery $query): bool
    {
        $versions = $query->getUser()->getVersions();

        $foundCurrentPolicyVersion = function (LogPolicy $version) {
            return ($version->getVersion() === $this->policyVersion);
        };
        $isFound = $versions->filter($foundCurrentPolicyVersion)->isEmpty();

        return false === $isFound;
    }

}