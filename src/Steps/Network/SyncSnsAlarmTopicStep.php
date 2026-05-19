<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncSnsAlarmTopicStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::alarmTopic();

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            $name = Helpers::keyedResourceName(exclusive: false);

            if (! Arr::get($options, 'dry-run')) {
                Aws::sns()->createTopic([
                    'Name' => $name,
                    ...Aws::tags([
                        'Name' => $name,
                    ]),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
