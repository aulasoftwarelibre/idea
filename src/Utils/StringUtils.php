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

namespace App\Utils;

use function array_fill;
use function array_map;
use function implode;
use function mb_strlen;
use function random_int;

final class StringUtils
{
    public static function locator(
        int $length = 8,
        string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
    ): string {
        return implode(
            '',
            array_map(
                static function ($i) use ($characters) {
                    return $characters[random_int(0, mb_strlen($characters) - 1)];
                },
                array_fill(0, $length, 0),
            ),
        );
    }
}
