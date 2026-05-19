<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Scheduler;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsScheduler;

class ExecuteSchedulerDeployStepsStep implements HasSubSteps, RunsOnAwsScheduler
{
    public function __invoke(): array
    {
        return Manifest::get('deploy', []);
    }
}
