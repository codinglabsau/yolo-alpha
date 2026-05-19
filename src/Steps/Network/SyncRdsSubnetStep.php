<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\Rds;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncRdsSubnetStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::dbSubnetGroup();

            if (Manifest::has('aws.rds.subnet')) {
                return StepResult::CUSTOM_MANAGED;
            }

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::rds()->createDBSubnetGroup([
                    'DBSubnetGroupName' => Helpers::keyedResourceName(Rds::PUBLIC_SUBNET_GROUP),
                    'DBSubnetGroupDescription' => 'YOLO private subnet group',
                    'SubnetIds' => collect(AwsResources::subnets())
                        ->pluck('SubnetId')
                        ->toArray(),
                    ...Aws::tags([
                        'Name' => Helpers::keyedResourceName(Rds::PUBLIC_SUBNET_GROUP),
                    ]),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
