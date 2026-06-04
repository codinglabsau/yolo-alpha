<?php

namespace Codinglabs\YoloAlpha\Steps\Tenant;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncQueueStep extends TenantStep
{
    public function __invoke(array $options): StepResult
    {
        $result = StepResult::SYNCED;

        foreach (Manifest::get('aws.sqs.queues', ['default']) as $type) {
            $name = $type === 'default'
                ? Helpers::keyedResourceName($this->tenantId())
                : Helpers::keyedResourceName(sprintf('%s-%s', $this->tenantId(), $type));

            try {
                AwsResources::queue($name);
            } catch (ResourceDoesNotExistException) {
                if (! Arr::get($options, 'dry-run')) {
                    Aws::sqs()->createQueue([
                        'QueueName' => $name,
                        'Attributes' => [
                            'MessageRetentionPeriod' => '1209600', // 14 days
                        ],
                        ...Aws::tags(wrap: 'tags', associative: true),
                    ]);

                    $result = StepResult::CREATED;
                } else {
                    $result = StepResult::WOULD_CREATE;
                }
            }
        }

        return $result;
    }
}
