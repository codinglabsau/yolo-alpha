<?php

namespace Codinglabs\YoloAlpha\Steps\Tenant;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncQueueStep extends TenantStep
{
    public function __invoke(array $options): StepResult
    {
        $name = Helpers::keyedResourceName($this->tenantId());

        try {
            AwsResources::queue($name);

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::sqs()->createQueue([
                    'QueueName' => $name,
                    'Attributes' => [
                        'MessageRetentionPeriod' => '1209600', // 14 days
                    ],
                    ...Aws::tags(wrap: 'tags', associative: true),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
