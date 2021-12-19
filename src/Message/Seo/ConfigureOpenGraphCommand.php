<?php

declare(strict_types=1);

namespace App\Message\Seo;

final class ConfigureOpenGraphCommand
{
    public function __construct(private int $ideaId)
    {
    }

    public function getIdeaId(): int
    {
        return $this->ideaId;
    }
}
