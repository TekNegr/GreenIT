<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\Logger;

class CustomizeSidebarFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $handler->setFormatter(new LineFormatter(
                "[%datetime%] %message% %context% %extra%\n",
                'Y-m-d H:i:s',
                true,
                true
            ));
        }
    }
}
