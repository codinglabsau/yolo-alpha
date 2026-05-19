<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Illuminate\Support\Str;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

trait UsesSns
{
    public static function alarmTopic(): array
    {
        return static::topicByName(Helpers::keyedResourceName(exclusive: false));
    }

    public static function topicByName(string $name): array
    {
        $topics = Aws::sns()->listTopics();

        foreach ($topics['Topics'] as $topic) {
            if (Str::afterLast($topic['TopicArn'], ':') === $name) {
                return $topic;
            }
        }

        throw new ResourceDoesNotExistException("Could not find SNS topic with name $name");
    }
}
