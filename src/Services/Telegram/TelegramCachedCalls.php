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

namespace App\Services\Telegram;

use Psr\Cache\CacheItemPoolInterface;
use Telegram\Bot\Api as Telegram;
use Telegram\Bot\Objects\User;

class TelegramCachedCalls
{
    /**
     * @var Telegram
     */
    private $telegram;
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

    public function __construct(Telegram $telegram, CacheItemPoolInterface $cache)
    {
        $this->telegram = $telegram;
        $this->cache = $cache;
    }

    public function getMe(): User
    {
        $key = 'telegram_get_me';
        $cachedMe = $this->cache->getItem($key);

        if (!$cachedMe->isHit()) {
            $me = $this->telegram->getMe();
            $cachedMe->set($me);

            $this->cache->save($cachedMe);
        }

        return $cachedMe->get();
    }
}
