<?php

namespace Codinglabs\YoloAlpha\Steps\Ci;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\UsesCodeDeploy;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncCodeDeployApplicationStep implements Step
{
    use UsesCodeDeploy;

    public function __invoke(array $options): StepResult
    {
        try {
            $application = AwsResources::application();

            if (! Arr::get($options, 'dry-run')) {
                // AWS allows updates to the application name only,
                // so we'll eager merge tags when syncing
                Aws::codeDeploy()->tagResource([
                    'ResourceArn' => static::arnForApplication($application),
                    ...Aws::tags(),
                ]);
            }

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::codeDeploy()->createApplication([
                    'applicationName' => static::applicationName(),
                    ...Aws::tags([
                        'Name' => static::applicationName(),
                    ], wrap: 'tags'),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
