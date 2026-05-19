<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class EnsureAutoscalingGroupSchedulerExistsStep implements Step
{
    public function __invoke(): StepResult
    {
        AwsResources::autoScalingGroupScheduler();

        return StepResult::SUCCESS;
    }
}
