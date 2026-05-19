<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\EnsuresResourcesExist;
use Codinglabs\YoloAlpha\Contracts\ExecutesStandaloneStep;

class EnsureHostedZonesExistStep implements ExecutesStandaloneStep
{
    use EnsuresResourcesExist;

    public function __invoke(array $options): StepResult
    {
        Manifest::get('apex')
            ? $this->ensure(fn () => AwsResources::hostedZone(Manifest::get('apex')))
            : $this->ensure(fn () => AwsResources::hostedZone(Manifest::get('domain')));

        return StepResult::SYNCED;
    }
}
