<?php

namespace Codinglabs\YoloAlpha\Steps;

use Codinglabs\YoloAlpha\Contracts\RunsOnAws;
use Codinglabs\YoloAlpha\Concerns\ExecutesCommands;
use Codinglabs\YoloAlpha\Contracts\ExecutesCommandStep;

class ExecuteCommandOnAwsStep implements ExecutesCommandStep, RunsOnAws
{
    use ExecutesCommands;
}
