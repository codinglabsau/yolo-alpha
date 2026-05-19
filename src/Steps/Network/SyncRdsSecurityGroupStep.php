<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\SecurityGroup;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncRdsSecurityGroupStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::rdsSecurityGroup();

            if (Manifest::has('aws.rds.security-group')) {
                return StepResult::CUSTOM_MANAGED;
            }

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                $name = Helpers::keyedResourceName(SecurityGroup::RDS_SECURITY_GROUP, exclusive: false);

                Aws::ec2()->createSecurityGroup([
                    'Description' => 'Enable EC2 to connect to RDS',
                    'GroupName' => $name,
                    'VpcId' => AwsResources::vpc()['VpcId'],
                    'TagSpecifications' => [
                        [
                            'ResourceType' => 'security-group',
                            ...Aws::tags([
                                'Name' => $name,
                            ]),
                        ],
                    ],
                ]);

                $securityGroup = AwsResources::rdsSecurityGroup();

                Aws::ec2()->authorizeSecurityGroupIngress([
                    'GroupId' => $securityGroup['GroupId'],
                    'IpPermissions' => [
                        [
                            // Enable EC2 to connect to RDS
                            'IpProtocol' => 'tcp',
                            'FromPort' => 3306,
                            'ToPort' => 3306,
                            'UserIdGroupPairs' => [
                                [
                                    'GroupId' => AwsResources::ec2SecurityGroup()['GroupId'],
                                    'Description' => 'Enable EC2 to connect to RDS',
                                ],
                            ],
                        ],
                    ],
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
