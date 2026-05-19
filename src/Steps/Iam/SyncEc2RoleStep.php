<?php

namespace Codinglabs\YoloAlpha\Steps\Iam;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncEc2RoleStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            AwsResources::ec2Role();

            if (! Arr::get($options, 'dry-run')) {
                $name = Helpers::keyedResourceName(exclusive: false);

                Aws::iam()->updateRole([
                    'RoleName' => $name,
                    'Description' => 'YOLO managed EC2 role',
                ]);

                Aws::iam()->updateAssumeRolePolicy([
                    'RoleName' => $name,
                    'PolicyDocument' => json_encode(AwsResources::ec2RolePolicyDocument()),
                ]);

                Aws::iam()->tagRole([
                    'RoleName' => $name,
                    ...Aws::tags(),
                ]);

                return StepResult::SYNCED;
            }

            return StepResult::WOULD_SYNC;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::iam()->createRole([
                    'RoleName' => Helpers::keyedResourceName(exclusive: false),
                    'Description' => 'YOLO managed EC2 role',
                    'AssumeRolePolicyDocument' => json_encode(AwsResources::ec2RolePolicyDocument()),
                    ...Aws::tags(),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
