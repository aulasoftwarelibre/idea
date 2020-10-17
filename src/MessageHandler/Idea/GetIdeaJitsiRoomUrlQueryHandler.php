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

namespace App\MessageHandler\Idea;

use App\Message\Idea\GetIdeaJitsiRoomUrlQuery;

final class GetIdeaJitsiRoomUrlQueryHandler
{
    /**
     * @var string
     */
    private $jitsiPrefix;

    public function __construct(string $jitsiPrefix)
    {
        $this->jitsiPrefix = $jitsiPrefix;
    }

    public function __invoke(GetIdeaJitsiRoomUrlQuery $query): ?string
    {
        $idea = $query->getIdea();

        if (!$idea->isOnline()) {
            return null;
        }

        return sprintf('https://meet.jit.si/%s-%s', $this->jitsiPrefix, $idea->getJitsiLocatorRoom());
    }
}
