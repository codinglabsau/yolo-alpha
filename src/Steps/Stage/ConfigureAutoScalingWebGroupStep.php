<?php

namespace Codinglabs\YoloAlpha\Steps\Stage;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Concerns\ConfiguresAutoScalingGroups;

class ConfigureAutoScalingWebGroupStep implements Step
{
    use ConfiguresAutoScalingGroups;

    public function __invoke(array $options): StepResult
    {
        if (! Arr::get($options, 'dry-run')) {
            if (Arr::get($options, 'update')) {
                static::updateAutoScalingGroup(ServerGroup::WEB);

                return StepResult::SYNCED;
            }

            $name = static::createAutoScalingGroup(ServerGroup::WEB);

            Manifest::put('aws.autoscaling.web', $name);

            Aws::autoscaling()->putScalingPolicy([
                'AutoScalingGroupName' => $name,
                'PolicyName' => "$name-up",
                'AdjustmentType' => 'ChangeInCapacity',
                'Cooldown' => 60,
                'ScalingAdjustment' => 2,
            ]);

            Aws::autoscaling()->putScalingPolicy([
                'AutoScalingGroupName' => $name,
                'PolicyName' => "$name-down",
                'AdjustmentType' => 'ChangeInCapacity',
                'Cooldown' => 300,
                'ScalingAdjustment' => -1,
            ]);

            return StepResult::CREATED;
        }

        return Arr::get($options, 'update')
            ? StepResult::WOULD_SYNC
            : StepResult::WOULD_CREATE;
    }
}
