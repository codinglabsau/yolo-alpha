<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Aws\EventBridge\Exception\EventBridgeException;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

trait UsesEventBridge
{
    protected static array $eventBridgeRules = [];

    public static function eventBridgeRule(string $name): array
    {
        if (isset(static::$eventBridgeRules[$name])) {
            return static::$eventBridgeRules[$name];
        }

        try {
            $result = Aws::eventBridge()->describeRule([
                'Name' => $name,
            ]);
        } catch (EventBridgeException $e) {
            throw new ResourceDoesNotExistException("Could not find EventBridge rule with name $name");
        }

        static::$eventBridgeRules[$name] = $result->toArray();

        return static::$eventBridgeRules[$name];
    }
}
