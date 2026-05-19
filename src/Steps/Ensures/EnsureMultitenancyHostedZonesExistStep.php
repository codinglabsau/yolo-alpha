<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Concerns\EnsuresResourcesExist;

class EnsureMultitenancyHostedZonesExistStep extends TenantStep
{
    use EnsuresResourcesExist;

    public function __invoke(array $options): StepResult
    {
        $this->ensure(fn () => AwsResources::hostedZone($this->config['apex']));

        return StepResult::SYNCED;
    }
}
