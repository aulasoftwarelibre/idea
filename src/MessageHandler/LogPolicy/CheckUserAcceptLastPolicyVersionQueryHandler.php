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

namespace App\MessageHandler\LogPolicy;

use App\Entity\LogPolicy;
use App\Message\LogPolicy\CheckUserAcceptLastPolicyVersionQuery;

class CheckUserAcceptLastPolicyVersionQueryHandler
{
    /**
     * @var string
     */
    private $policyVersion;

    public function __construct(string $policyVersion)
    {
        $this->policyVersion = $policyVersion;
    }

    public function __invoke(CheckUserAcceptLastPolicyVersionQuery $query): bool
    {
        $versions = $query->getUser()->getVersions();

        $foundCurrentPolicyVersion = function (LogPolicy $version) {
            return $version->getVersion() === $this->policyVersion;
        };

        $isFound = $versions->filter($foundCurrentPolicyVersion)->isEmpty();

        return false === $isFound;
    }
}
