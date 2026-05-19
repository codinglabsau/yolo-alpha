<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;

class SetOwnershipAndPermissionsStep implements HasSubSteps, RunsOnAws
{
    public function __invoke(): array
    {
        $name = Manifest::name();

        return [
            'chown -R ubuntu:ubuntu /home/ubuntu',
            'chown -R ubuntu:ubuntu /var/log/yolo',
            "chown -R ubuntu:ubuntu /var/www/$name",
            "chmod -R 757 /var/www/$name/storage",
        ];
    }
}
