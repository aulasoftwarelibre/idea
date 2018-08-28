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
use Sgomez\Bundle\BotmanBundle\Model\Telegram\User;
use Sgomez\Bundle\BotmanBundle\Services\Http\TelegramClient;

class TelegramCachedCalls
{
    /**
     * @var CacheItemPoolInterface
     */
    private $cache;
    /**
     * @var TelegramClient
     */
    private $client;

    public function __construct(TelegramClient $client, CacheItemPoolInterface $cache)
    {
        $this->cache = $cache;
        $this->client = $client;
    }

    public function getMe(): User
    {
        $key = 'telegram_get_me';
        $cachedMe = $this->cache->getItem($key);

        if (!$cachedMe->isHit()) {
            $me = $this->client->getMe();
            $cachedMe->set($me);

            $this->cache->save($cachedMe);
        }

        return $cachedMe->get();
    }
}
