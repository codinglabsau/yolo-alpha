<?php

namespace Codinglabs\YoloAlpha\Steps\Iam;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\Iam;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncMediaConvertRoleStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        if (! Manifest::get('aws.mediaconvert')) {
            return StepResult::SKIPPED;
        }

        try {
            AwsResources::mediaConvertRole();

            if (! Arr::get($options, 'dry-run')) {
                $name = Helpers::keyedResourceName(Iam::MEDIA_CONVERT_ROLE);

                Aws::iam()->updateRole([
                    'RoleName' => $name,
                    'Description' => 'YOLO managed MediaConvert role',
                ]);

                Aws::iam()->updateAssumeRolePolicy([
                    'RoleName' => $name,
                    'PolicyDocument' => json_encode(AwsResources::mediaConvertPolicyDocument()),
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
                    'RoleName' => Helpers::keyedResourceName(Iam::MEDIA_CONVERT_ROLE),
                    'Description' => 'YOLO managed MediaConvert role',
                    'AssumeRolePolicyDocument' => json_encode(AwsResources::mediaConvertPolicyDocument()),
                    ...Aws::tags(),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
