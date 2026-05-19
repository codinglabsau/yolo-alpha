<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\EnsuresResourcesExist;

class EnsureKeyPairExistsStep implements Step
{
    use EnsuresResourcesExist;

    public function __invoke(): StepResult
    {
        $this->ensure(fn () => AwsResources::keyPair());

        return StepResult::SYNCED;
    }
}
