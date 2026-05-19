<?php

namespace Codinglabs\YoloAlpha\Steps\Iam;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Enums\Iam;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncEc2InstanceProfileStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        $name = Helpers::keyedResourceName(Iam::INSTANCE_PROFILE, exclusive: false);

        try {
            AwsResources::ec2InstanceProfile();

            if (! Arr::get($options, 'dry-run')) {
                Aws::iam()->tagInstanceProfile([
                    'InstanceProfileName' => $name,
                    ...Aws::tags(),
                ]);

                return StepResult::SYNCED;
            }

            return StepResult::WOULD_SYNC;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::iam()->createInstanceProfile([
                    'InstanceProfileName' => $name,
                    ...Aws::tags(),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
