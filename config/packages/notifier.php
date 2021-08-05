<?php

declare(strict_types=1);

use Symfony\Config\FrameworkConfig;

return static function (FrameworkConfig $framework): void {
    $framework->notifier()
        ->channelPolicy('urgent', ['email'])
        ->channelPolicy('high', ['email'])
        ->channelPolicy('medium', ['email'])
        ->channelPolicy('low', ['email'])
    ;

    $framework->notifier()
        ->adminRecipient()
        ->email('%mail_from%');
};
