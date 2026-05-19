<?php

namespace Codinglabs\YoloAlpha\Steps\Landlord;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\ExecutesMultitenancyStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncQueueStep implements ExecutesMultitenancyStep
{
    public function __invoke(array $options): StepResult
    {
        $name = Helpers::keyedResourceName('landlord');

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
