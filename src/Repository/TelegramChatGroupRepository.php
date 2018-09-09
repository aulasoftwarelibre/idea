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

namespace App\Repository;

use App\Entity\TelegramChat;
use App\Entity\TelegramChatGroup;
use App\Entity\TelegramChatSuperGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TelegramChatGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TelegramChatGroup::class);
    }

    public function add(TelegramChat $thread): void
    {
        $this->_em->persist($thread);
    }

    public function remove(TelegramChat $thread): void
    {
        $this->_em->remove($thread);
    }
}
