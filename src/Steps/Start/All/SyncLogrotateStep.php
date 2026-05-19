<?php

namespace Codinglabs\YoloAlpha\Steps\Start\All;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;

class SyncLogrotateStep implements RunsOnAws
{
    public function __invoke(): StepResult
    {
        file_put_contents(
            sprintf('/etc/logrotate.d/%s', Helpers::keyedResourceName()),
            str_replace(
                search: [
                    '{NAME}',
                ],
                replace: [
                    Manifest::name(),
                ],
                subject: file_get_contents(Paths::stubs('logrotate/laravel.stub'))
            )
        );

        file_put_contents(
            '/etc/logrotate.d/yolo',
            file_get_contents(Paths::stubs('logrotate/yolo.stub'))
        );

        return StepResult::SYNCED;
    }
}
