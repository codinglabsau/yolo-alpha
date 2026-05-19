<?php

namespace Codinglabs\YoloAlpha\Steps\Iam;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class AttachMediaConvertRolePoliciesStep implements Step
{
    protected array $managedPolicies = [
        'arn:aws:iam::aws:policy/AmazonAPIGatewayInvokeFullAccess',
        'arn:aws:iam::aws:policy/AmazonS3FullAccess',
    ];

    public function __invoke(array $options): StepResult
    {
        if (! Manifest::get('aws.mediaconvert')) {
            return StepResult::SKIPPED;
        }

        if (! Arr::get($options, 'dry-run')) {
            $role = AwsResources::mediaConvertRole();

            foreach ($this->managedPolicies as $policyArn) {
                Aws::iam()->attachRolePolicy([
                    'RoleName' => $role['RoleName'],
                    'PolicyArn' => $policyArn,
                ]);
            }

            return StepResult::SYNCED;
        }

        return StepResult::WOULD_SYNC;
    }
}
