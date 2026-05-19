<?php

namespace Codinglabs\YoloAlpha\Steps\Logging;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncIvsEventBridgeRuleStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        if (! Manifest::ivsEnabled()) {
            return StepResult::SKIPPED;
        }

        $name = self::ruleName();

        try {
            AwsResources::eventBridgeRule($name);

            if (! Arr::get($options, 'dry-run')) {
                Aws::eventBridge()->putRule([
                    'Name' => $name,
                    'Description' => 'YOLO managed IVS state change events',
                    'EventPattern' => json_encode(self::eventPattern()),
                    'State' => 'ENABLED',
                    ...Aws::tags([
                        'Name' => $name,
                    ]),
                ]);

                return StepResult::SYNCED;
            }

            return StepResult::WOULD_SYNC;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::eventBridge()->putRule([
                    'Name' => $name,
                    'Description' => 'YOLO managed IVS state change events',
                    'EventPattern' => json_encode(self::eventPattern()),
                    'State' => 'ENABLED',
                    ...Aws::tags([
                        'Name' => $name,
                    ]),
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }

    public static function ruleName(): string
    {
        return Helpers::keyedResourceName('ivs-state-change');
    }

    public static function eventPattern(): array
    {
        return [
            'source' => ['aws.ivs'],
        ];
    }
}
