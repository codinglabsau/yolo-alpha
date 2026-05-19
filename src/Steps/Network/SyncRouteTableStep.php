<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncRouteTableStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::routeTable();

            if (Manifest::has('aws.route-table')) {
                return StepResult::CUSTOM_MANAGED;
            }

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::ec2()->createRouteTable([
                    'VpcId' => AwsResources::vpc()['VpcId'],
                    'TagSpecifications' => [
                        [
                            'ResourceType' => 'route-table',
                            ...Aws::tags([
                                'Name' => Helpers::keyedResourceName(exclusive: false),
                            ]),
                        ],
                    ],
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
