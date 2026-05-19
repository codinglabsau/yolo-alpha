<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;

class ProvisionDirectoriesStep implements HasSubSteps, RunsOnAws
{
    public function __invoke(): array
    {
        return [
            sprintf('mkdir -p %s', Paths::yoloDir()),
            sprintf('mkdir -p %s', Paths::logDir()),
        ];
    }
}
