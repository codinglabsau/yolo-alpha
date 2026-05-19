<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Scheduler;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsScheduler;

class SyncSchedulerCronStep implements RunsOnAwsScheduler
{
    public function __invoke(array $options): StepResult
    {
        file_put_contents(
            sprintf('/etc/cron.d/%s', Helpers::keyedResourceName(ServerGroup::SCHEDULER)),
            str_replace(
                search: [
                    '{NAME}',
                ],
                replace: [
                    Manifest::name(),
                ],
                subject: file_get_contents(Paths::stubs('cron/scheduler.stub'))
            )
        );

        return StepResult::SYNCED;
    }
}
