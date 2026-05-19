<?php

namespace Codinglabs\YoloAlpha\Steps\Iam;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class AttachEc2RolePoliciesStep implements Step
{
    protected array $managedPolicies = [
        'arn:aws:iam::aws:policy/AmazonRekognitionReadOnlyAccess',
        'arn:aws:iam::aws:policy/AWSElementalMediaConvertFullAccess',
        'arn:aws:iam::aws:policy/IVSFullAccess',
    ];

    public function __invoke(array $options): StepResult
    {
        if (! Arr::get($options, 'dry-run')) {
            $policy = AwsResources::ec2Policy();
            $role = AwsResources::ec2Role();

            Aws::iam()->attachRolePolicy([
                'RoleName' => $role['RoleName'],
                'PolicyArn' => $policy['Arn'],
            ]);

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
