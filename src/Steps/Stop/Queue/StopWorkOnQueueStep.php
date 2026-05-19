<?php

namespace Codinglabs\YoloAlpha\Steps\Stop\Queue;

use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue;
use Codinglabs\YoloAlpha\Concerns\InteractsWithSupervisor;

class StopWorkOnQueueStep implements RunsOnAwsQueue
{
    use InteractsWithSupervisor;

    public function __invoke(): StepResult
    {
        $this->stopSupervisorWorkers();

        return StepResult::SUCCESS;
    }
}
