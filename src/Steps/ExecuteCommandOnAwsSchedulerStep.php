<?php

namespace Codinglabs\YoloAlpha\Steps;

use Codinglabs\YoloAlpha\Concerns\ExecutesCommands;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsScheduler;
use Codinglabs\YoloAlpha\Contracts\ExecutesCommandStep;

class ExecuteCommandOnAwsSchedulerStep implements ExecutesCommandStep, RunsOnAwsScheduler
{
    use ExecutesCommands;
}
