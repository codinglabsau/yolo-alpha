<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;

trait CreatesSubnets
{
    public function createSubnet(string $name, int $index): void
    {
        $vpc = AwsResources::vpc();
        $availabilityZones = AwsResources::availabilityZones(Manifest::get('aws.region'));

        Aws::ec2()->createSubnet([
            'AvailabilityZone' => $availabilityZones[$index]['ZoneName'],
            'CidrBlock' => "10.1.$index.0/24",
            'VpcId' => $vpc['VpcId'],
            'MapPublicIpOnLaunch' => true,
            'TagSpecifications' => [
                [
                    'ResourceType' => 'subnet',
                    ...Aws::tags([
                        'Name' => Helpers::keyedResourceName($name, exclusive: false),
                    ]),
                ],
            ],
        ]);
    }
}
