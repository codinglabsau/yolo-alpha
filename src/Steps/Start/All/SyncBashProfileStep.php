<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;

class SyncBashProfileStep implements RunsOnAws
{
    public function __invoke(array $options): StepResult
    {
        file_put_contents(
            '/home/ubuntu/.bash_profile',
            file_get_contents(Paths::stubs('.bash_profile.stub'))
        );

        return StepResult::SYNCED;
    }
}
