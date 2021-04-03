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

namespace App\Messenger\IdeaMessage;

final class SendIdeaMessageCommand
{
    /**
     * @var int
     */
    private $ideaId;
    /**
     * @var string
     */
    private $message;
    /**
     * @var bool
     */
    private $isTest;

    public function __construct(int $ideaId, string $message, bool $isTest)
    {
        $this->ideaId = $ideaId;
        $this->message = $message;
        $this->isTest = $isTest;
    }

    /**
     * @return int
     */
    public function getIdeaId(): int
    {
        return $this->ideaId;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isTest(): bool
    {
        return $this->isTest;
    }
}
