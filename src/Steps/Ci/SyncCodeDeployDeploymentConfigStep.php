<?php

namespace Codinglabs\YoloAlpha\Steps\Ci;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncCodeDeployDeploymentConfigStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::OneThirdAtATimeDeploymentConfig();

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::codeDeploy()->createDeploymentConfig([
                    'deploymentConfigName' => 'OneThirdAtATime',
                    'computePlatform' => 'Server',
                    'minimumHealthyHosts' => [
                        'type' => 'FLEET_PERCENT',
                        'value' => 60,
                    ],
                    ...Aws::tags([
                        'Name' => Helpers::keyedResourceName('OneThirdAtATime', exclusive: false),
                    ]),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
