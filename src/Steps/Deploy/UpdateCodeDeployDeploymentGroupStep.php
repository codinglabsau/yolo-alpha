<?php

namespace Codinglabs\YoloAlpha\Steps\Deploy;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Concerns\UsesCodeDeploy;
use Codinglabs\YoloAlpha\Concerns\ParsesOnlyOption;

class UpdateCodeDeployDeploymentGroupStep implements Step
{
    use ParsesOnlyOption;
    use UsesCodeDeploy;

    public function __invoke(array $options): StepResult
    {
        if ($this->shouldRunOnGroup(ServerGroup::WEB, $options)) {
            Aws::codeDeploy()->updateDeploymentGroup([
                'applicationName' => static::applicationName(),
                'currentDeploymentGroupName' => Helpers::keyedResourceName(ServerGroup::WEB),
                'autoScalingGroups' => [
                    Manifest::get('aws.autoscaling.web'),
                ],
            ]);
        }

        if ($this->shouldRunOnGroup(ServerGroup::QUEUE, $options)) {
            Aws::codeDeploy()->updateDeploymentGroup([
                'applicationName' => static::applicationName(),
                'currentDeploymentGroupName' => Helpers::keyedResourceName(ServerGroup::QUEUE),
                'autoScalingGroups' => [
                    Manifest::get('aws.autoscaling.queue'),
                ],
            ]);
        }

        if ($this->shouldRunOnGroup(ServerGroup::SCHEDULER, $options)) {
            Aws::codeDeploy()->updateDeploymentGroup([
                'applicationName' => static::applicationName(),
                'currentDeploymentGroupName' => Helpers::keyedResourceName(ServerGroup::SCHEDULER),
                'autoScalingGroups' => [
                    Manifest::get('aws.autoscaling.scheduler'),
                ],
            ]);
        }

        return StepResult::SUCCESS;
    }
}
