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

namespace App\Message\User;

final class RemoveUserCommand
{
    public function __construct(private string $username, private bool $hardDelete)
    {
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isHardDelete(): bool
    {
        return $this->hardDelete;
    }
}
