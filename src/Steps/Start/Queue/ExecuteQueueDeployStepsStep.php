<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Queue;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue;

class ExecuteQueueDeployStepsStep implements HasSubSteps, RunsOnAwsQueue
{
    public function __invoke(): array
    {
        return Manifest::get('deploy-queue', []);
    }
}
