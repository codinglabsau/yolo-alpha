<?php

namespace Codinglabs\YoloAlpha\Steps\Image;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Concerns\UsesEc2;
use Codinglabs\YoloAlpha\Enums\StepResult;

class TerminateAmiInstanceStep implements Step
{
    use UsesEc2;

    public function __invoke(): StepResult
    {
        Aws::ec2()->terminateInstances([
            'InstanceIds' => [
                Helpers::app('amiInstanceId'),
            ],
        ]);

        return StepResult::SUCCESS;
    }
}
