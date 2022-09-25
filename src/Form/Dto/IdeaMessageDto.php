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

namespace App\Form\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class IdeaMessageDto
{
    #[Assert\NotBlank]
    private string|null $message = '';

    private bool|null $isTest = true;

    public function __construct()
    {
    }

    public function getMessage(): string
    {
        return (string) $this->message;
    }

    /** @return IdeaMessageDto */
    public function setMessage(string|null $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsTest(): bool
    {
        return (bool) $this->isTest;
    }

    /** @return IdeaMessageDto */
    public function setIsTest(bool|null $isTest): self
    {
        $this->isTest = $isTest;

        return $this;
    }
}
