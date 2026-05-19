<?php

namespace Codinglabs\YoloAlpha\Steps\Compute;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncLaunchTemplateStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            // ensure the launch template exists; refer to "yolo image:create"
            // to create new launch template versions with synced attributes.
            AwsResources::launchTemplate();

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::ec2()->createLaunchTemplate(
                    AwsResources::launchTemplatePayload(),
                );

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
