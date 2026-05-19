<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;

class ExecuteAllGroupsDeployStepsStep implements HasSubSteps, RunsOnAws
{
    public function __invoke(): array
    {
        return Manifest::get('deploy-all', []);
    }
}
