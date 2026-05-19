<?php

namespace Codinglabs\YoloAlpha\Steps\Ci;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Concerns\UsesCodeDeploy;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncCodeDeployWebDeploymentGroupStep implements Step
{
    use UsesCodeDeploy;

    public function __invoke(array $options): StepResult
    {
        try {
            $deploymentGroup = AwsResources::webDeploymentGroup();

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
                static::applyTagsToDeploymentGroup(AwsResources::webDeploymentGroup());

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
                'deploymentGroupName' => Helpers::keyedResourceName(ServerGroup::WEB),
                'deploymentConfigName' => Manifest::get('aws.codedeploy.with-load-balancing', false)
                    ? 'OneThirdAtATime'
                    : 'CodeDeployDefault.AllAtOnce',
                'autoScalingGroups' => [
                    AwsResources::autoScalingGroupWeb()['AutoScalingGroupName'],
                ],
                'deploymentStyle' => [
                    'deploymentType' => 'IN_PLACE',
                    'deploymentOption' => Manifest::get('aws.codedeploy.with-load-balancing', false)
                        ? 'WITH_TRAFFIC_CONTROL'
                        : 'WITHOUT_TRAFFIC_CONTROL',
                ],
                'loadBalancerInfo' => [
                    'targetGroupInfoList' => [
                        [
                            'name' => AwsResources::targetGroup()['TargetGroupName'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
