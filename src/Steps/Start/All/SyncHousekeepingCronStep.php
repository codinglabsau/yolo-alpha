<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;

class SyncHousekeepingCronStep implements RunsOnAws
{
    public function __invoke(array $options): StepResult
    {
        file_put_contents(
            '/etc/cron.d/yolo-housekeeping',
            file_get_contents(Paths::stubs('cron/housekeeping.stub'))
        );

        return StepResult::SYNCED;
    }
}
