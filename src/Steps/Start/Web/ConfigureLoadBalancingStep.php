<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Web;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;
use Codinglabs\YoloAlpha\Concerns\UsesAutoscaling;

class ConfigureLoadBalancingStep implements RunsOnAwsWeb
{
    use UsesAutoscaling;

    public function __invoke(): void
    {
        // ensure the web ASG is attached to the ALB
        $asgWeb = AwsResources::autoScalingGroupWeb();

        Aws::autoscaling()->attachTrafficSources([
            'AutoScalingGroupName' => $asgWeb['AutoScalingGroupName'],
            'TrafficSources' => [
                [
                    'Identifier' => AwsResources::targetGroup()['TargetGroupArn'],
                    'Type' => 'elbv2',
                ],
            ],
        ]);

        Aws::autoscaling()->updateAutoScalingGroup([
            'AutoScalingGroupName' => $asgWeb['AutoScalingGroupName'],
            'HealthCheckType' => 'ELB',
            'HealthCheckGracePeriod' => 60,
        ]);
    }
}
