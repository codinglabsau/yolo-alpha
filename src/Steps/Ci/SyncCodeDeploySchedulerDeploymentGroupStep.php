<?php

namespace Codinglabs\YoloAlpha\Steps\Ci;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Concerns\UsesCodeDeploy;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncCodeDeploySchedulerDeploymentGroupStep implements Step
{
    use UsesCodeDeploy;

    public function __invoke(array $options): StepResult
    {
        try {
            $deploymentGroup = AwsResources::schedulerDeploymentGroup();

            $differences = Helpers::payloadHasDifferences(
                expected: $this->payload(),
                actual: static::normaliseDeploymentGroupForComparison($deploymentGroup)
            );

            if (! Arr::get($options, 'dry-run')) {
                // always sync tags as they are not compared in the payload
                static::applyTagsToDeploymentGroup($deploymentGroup);

                if ($differences) {
                    Aws::codeDeploy()->updateDeploymentGroup([
                        'currentDeploymentGroupName' => $deploymentGroup['deploymentGroupName'],
                        ...$this->payload(),
                    ]);

                    return StepResult::SYNCED;
                }
            }

            return $differences
                ? StepResult::OUT_OF_SYNC
                : StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::codeDeploy()->createDeploymentGroup($this->payload());
                static::applyTagsToDeploymentGroup(AwsResources::schedulerDeploymentGroup());

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }

    protected function payload(): array
    {
        return [
            ...static::deploymentGroupPayload(),
            ...[
                'deploymentGroupName' => Helpers::keyedResourceName(ServerGroup::SCHEDULER),
                'deploymentConfigName' => 'CodeDeployDefault.AllAtOnce',
                'autoScalingGroups' => [
                    AwsResources::autoScalingGroupScheduler()['AutoScalingGroupName'],
                ],
                'deploymentStyle' => [
                    'deploymentType' => 'IN_PLACE',
                    'deploymentOption' => 'WITHOUT_TRAFFIC_CONTROL',
                ],
            ],
        ];
    }
}
