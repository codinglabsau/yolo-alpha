<?php

namespace Codinglabs\YoloAlpha\Steps\Image;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Concerns\UsesEc2;
use Codinglabs\YoloAlpha\Enums\StepResult;

class StopAmiInstanceStep implements Step
{
    use UsesEc2;

    public function __invoke(): StepResult
    {
        Aws::ec2()->stopInstances([
            'InstanceIds' => [Helpers::app('amiInstanceId')],
        ]);

        while (true) {
            // wait for instance to stop
            if (AwsResources::ec2ByName(Helpers::keyedResourceName('ami'), states: ['stopped'], throws: false)) {
                break;
            }

            sleep(3);
        }

        return StepResult::SUCCESS;
    }
}
