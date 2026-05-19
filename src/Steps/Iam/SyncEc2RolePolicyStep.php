<?php

namespace Codinglabs\YoloAlpha\Steps\Iam;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncEc2RolePolicyStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            $policy = AwsResources::ec2Policy();

            $currentPolicyDocument = json_decode(
                urldecode(
                    Aws::iam()->getPolicyVersion([
                        'PolicyArn' => $policy['Arn'],
                        'VersionId' => $policy['DefaultVersionId'],
                    ])['PolicyVersion']['Document']
                ),
                associative: true
            );

            $hasDifferences = Helpers::payloadHasDifferences($currentPolicyDocument, AwsResources::ec2PolicyDocument());

            if (! Arr::get($options, 'dry-run')) {
                if ($hasDifferences) {
                    Aws::iam()->createPolicyVersion([
                        'PolicyArn' => $policy['Arn'],
                        'PolicyDocument' => json_encode(AwsResources::ec2PolicyDocument()),
                        'SetAsDefault' => true,
                    ]);

                    return StepResult::SYNCED;
                }

                return StepResult::SYNCED;
            }

            return $hasDifferences
                ? StepResult::OUT_OF_SYNC
                : StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::iam()->createPolicy([
                    'PolicyName' => Helpers::keyedResourceName(exclusive: false),
                    'Description' => 'YOLO managed EC2 policy',
                    'PolicyDocument' => json_encode(AwsResources::ec2PolicyDocument()),
                    ...Aws::tags(),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
