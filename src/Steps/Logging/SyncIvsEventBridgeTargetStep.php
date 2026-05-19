<?php

namespace Codinglabs\YoloAlpha\Steps\Logging;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncIvsEventBridgeTargetStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        if (! Manifest::ivsEnabled()) {
            return StepResult::SKIPPED;
        }

        $ruleName = SyncIvsEventBridgeRuleStep::ruleName();
        $logGroupName = SyncIvsCloudWatchLogGroupStep::logGroupName();

        $region = Manifest::get('aws.region');
        $accountId = Aws::accountId();
        $expectedArn = "arn:aws:logs:{$region}:{$accountId}:log-group:{$logGroupName}";

        $existingTarget = null;

        try {
            AwsResources::eventBridgeRule($ruleName);

            $existingTarget = collect(Aws::eventBridge()->listTargetsByRule([
                'Rule' => $ruleName,
            ])['Targets'])->first(
                fn ($target) => $target['Id'] === 'ivs-cloudwatch-logs'
            );

            if ($existingTarget && $existingTarget['Arn'] === $expectedArn) {
                return StepResult::SYNCED;
            }
        } catch (ResourceDoesNotExistException) {
            // Rule doesn't exist yet — target needs to be created
        }

        if (! Arr::get($options, 'dry-run')) {
            Aws::eventBridge()->putTargets([
                'Rule' => $ruleName,
                'Targets' => [
                    [
                        'Id' => 'ivs-cloudwatch-logs',
                        'Arn' => $expectedArn,
                    ],
                ],
            ]);

            return $existingTarget
                ? StepResult::SYNCED
                : StepResult::CREATED;
        }

        return $existingTarget
            ? StepResult::WOULD_SYNC
            : StepResult::WOULD_CREATE;
    }
}
