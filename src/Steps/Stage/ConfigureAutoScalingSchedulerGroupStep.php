<?php

namespace Codinglabs\YoloAlpha\Steps\Stage;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Concerns\ConfiguresAutoScalingGroups;

class ConfigureAutoScalingSchedulerGroupStep implements Step
{
    use ConfiguresAutoScalingGroups;

    public function __invoke(array $options): StepResult
    {
        if (! Arr::get($options, 'dry-run')) {
            if (! Manifest::get('aws.autoscaling.combine', false)) {
                if (Arr::get($options, 'update')) {
                    static::updateAutoScalingGroup(ServerGroup::SCHEDULER);

                    return StepResult::SYNCED;
                }

                Manifest::put('aws.autoscaling.scheduler', static::createAutoScalingGroup(ServerGroup::SCHEDULER));

                return StepResult::CREATED;
            }

            // use the web ASG for the scheduler
            Manifest::put('aws.autoscaling.scheduler', Manifest::get('aws.autoscaling.web'));

            return StepResult::SYNCED;
        }

        return Arr::get($options, 'update')
            ? StepResult::WOULD_SYNC
            : StepResult::WOULD_CREATE;
    }
}
