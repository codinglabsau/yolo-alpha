<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class SyncDefaultRouteStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        // note: there does not appear to be a way to retrieve this resource directly, and
        // calling createRoute() multiple times does not create additional resources. This
        // resource is visible in the AWS console under VPC -> Route Tables -> $route > Routes.
        if (! Arr::get($options, 'dry-run')) {
            Aws::ec2()->createRoute([
                'DestinationCidrBlock' => '0.0.0.0/0',
                'GatewayId' => AwsResources::internetGateway()['InternetGatewayId'],
                'RouteTableId' => AwsResources::routeTable()['RouteTableId'],
            ]);

            return StepResult::SYNCED;
        }

        return StepResult::WOULD_SYNC;
    }
}
