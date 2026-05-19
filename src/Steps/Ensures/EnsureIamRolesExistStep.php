<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\EnsuresResourcesExist;

class EnsureIamRolesExistStep implements Step
{
    use EnsuresResourcesExist;

    public function __invoke(): StepResult
    {
        $this->ensure(fn () => AwsResources::ec2Role());

        if (Manifest::get('aws.mediaconvert')) {
            $this->ensure(fn () => AwsResources::mediaConvertRole());
        }

        return StepResult::SUCCESS;
    }
}
