<?php

namespace Codinglabs\YoloAlpha\Steps\Standalone;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\ExecutesDomainStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncHostedZoneStep implements ExecutesDomainStep
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::hostedZone(Manifest::apex());

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::route53()->createHostedZone([
                    'CallerReference' => Str::uuid(),
                    'Name' => Manifest::apex(),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
