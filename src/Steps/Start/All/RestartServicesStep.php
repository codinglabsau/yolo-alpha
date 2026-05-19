<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Contracts\RunsOnAws;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;

class RestartServicesStep implements HasSubSteps, RunsOnAws
{
    public function __invoke(): array
    {
        return [
            'supervisorctl reread',
            'supervisorctl update',
            'supervisorctl start all',
            'systemctl restart php8.3-fpm',
            'systemctl restart nginx',
        ];
    }
}
