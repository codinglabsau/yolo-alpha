<?php

namespace Codinglabs\YoloAlpha\Steps\Tenant;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncHostedZoneStep extends TenantStep
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::hostedZone($this->config['apex']);

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::route53()->createHostedZone([
                    'CallerReference' => Str::uuid(),
                    'Name' => $this->config['apex'],
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
