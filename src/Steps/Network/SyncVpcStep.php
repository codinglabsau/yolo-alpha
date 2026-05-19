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

class SyncVpcStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::vpc();

            if (Manifest::has('aws.vpc')) {
                return StepResult::CUSTOM_MANAGED;
            }

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::ec2()->createVpc([
                    'CidrBlock' => '10.1.0.0/16', // using 10.1 block instead of 10.0 to avoid conflicts with vapor
                    'TagSpecifications' => [
                        [
                            'ResourceType' => 'vpc',
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
