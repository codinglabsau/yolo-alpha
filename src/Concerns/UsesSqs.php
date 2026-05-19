<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Illuminate\Support\Str;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

trait UsesSqs
{
    public static function queue(string $queueName): array
    {
        $queues = Aws::sqs()->listQueues();

        foreach ($queues['QueueUrls'] as $queueUrl) {
            if (Str::afterLast($queueUrl, '/') === $queueName) {
                // listQueues() returns only queue URLs, so
                // we need to query additional details.
                return [
                    'QueueUrl' => $queueUrl, // AWS does not have this
                    ...Aws::sqs()->getQueueAttributes([
                        'QueueUrl' => $queueUrl,
                        'AttributeNames' => ['All'],
                    ])->toArray(),
                ];
            }
        }

        throw new ResourceDoesNotExistException("Could not find queue with name $queueName");
    }
}
