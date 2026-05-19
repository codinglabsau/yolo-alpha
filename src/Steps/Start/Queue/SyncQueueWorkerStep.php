<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Queue;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue;
use Codinglabs\YoloAlpha\Contracts\ExecutesStandaloneStep;

class SyncQueueWorkerStep implements ExecutesStandaloneStep, RunsOnAwsQueue
{
    public function __invoke(): StepResult
    {
        file_put_contents(
            sprintf('/etc/supervisor/conf.d/%s', Helpers::keyedResourceName('queue-worker.conf')),
            str_replace(
                search: [
                    '{NAME}',
                    '{AWS_SQS_ENDPOINT}',
                ],
                replace: [
                    Manifest::name(),
                    AwsResources::queue(Helpers::keyedResourceName())['QueueUrl'],
                ],
                subject: file_get_contents(Paths::stubs('supervisor/landlord-queue-worker.conf.stub'))
            )
        );

        return StepResult::SYNCED;
    }
}
