<?php

namespace Codinglabs\YoloAlpha\Steps;

use Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue;
use Codinglabs\YoloAlpha\Concerns\ExecutesCommands;
use Codinglabs\YoloAlpha\Contracts\ExecutesCommandStep;

class ExecuteCommandOnAwsQueueStep implements ExecutesCommandStep, RunsOnAwsQueue
{
    use ExecutesCommands;
}
