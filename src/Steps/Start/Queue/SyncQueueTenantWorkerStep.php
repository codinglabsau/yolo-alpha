<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Queue;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue;

class SyncQueueTenantWorkerStep extends TenantStep implements RunsOnAwsQueue
{
    public function __invoke(array $options): StepResult
    {
        file_put_contents(
            sprintf('/etc/supervisor/conf.d/%s', Helpers::keyedResourceName("{$this->tenantId()}-queue-worker.conf")),
            str_replace(
                search: [
                    '{NAME}',
                    '{TENANT}',
                    '{AWS_SQS_ENDPOINT}',
                ],
                replace: [
                    Manifest::name(),
                    $this->tenantId(),
                    AwsResources::queue(Helpers::keyedResourceName($this->tenantId()))['QueueUrl'],
                ],
                subject: file_get_contents(Paths::stubs('supervisor/tenant-queue-worker.conf.stub'))
            )
        );

        return StepResult::SYNCED;
    }
}
