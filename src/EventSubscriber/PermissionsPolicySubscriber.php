<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class PermissionsPolicySubscriber implements EventSubscriberInterface
{
    public function addPermissionsPolicyHeader(ResponseEvent $event): void
    {
        $event->getResponse()->headers->set('permissions-policy', 'interest-cohort=()');
    }

    /** @inheritDoc */
    public static function getSubscribedEvents(): array
    {
        return [ResponseEvent::class => 'addPermissionsPolicyHeader'];
    }
}
