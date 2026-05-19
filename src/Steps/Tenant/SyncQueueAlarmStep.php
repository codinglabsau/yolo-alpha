<?php

namespace Codinglabs\YoloAlpha\Steps\Tenant;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncQueueAlarmStep extends TenantStep
{
    public function __invoke(array $options): StepResult
    {
        $alarmName = Helpers::keyedResourceName(sprintf('%s-queue-depth-alarm', $this->tenantId()));

        try {
            AwsResources::alarm($alarmName);
        } catch (ResourceDoesNotExistException) {
            // CloudWatch accepts an upsert operation, so we'll
            // always sync the alarm with the desired state.
        }

        $snsTopic = AwsResources::alarmTopic();

        if (Arr::get($options, 'dry-run')) {
            return StepResult::WOULD_SYNC;
        }

        Aws::cloudWatch()->putMetricAlarm([
            'ActionsEnabled' => true,
            'AlarmName' => $alarmName,
            'AlarmDescription' => 'Alarm if queue is too deep. Created by yolo CLI',
            'ComparisonOperator' => 'GreaterThanThreshold',
            'Dimensions' => [
                [
                    'Name' => 'QueueName',
                    'Value' => Helpers::keyedResourceName($this->tenantId()),
                ],
            ],
            'EvaluationPeriods' => Manifest::get('aws.sqs.depth-alarm-evaluation-periods', 3), // number of breaches of the Period before alarm
            'MetricName' => 'ApproximateNumberOfMessagesVisible',
            'Namespace' => 'AWS/SQS',
            'Period' => Manifest::get('aws.sqs.depth-alarm-period', 300), // time to evaluate the metric
            'Statistic' => 'Average',
            'Threshold' => Manifest::get('aws.sqs.depth-alarm-threshold', 100),
            'TreatMissingData' => 'notBreaching',
            'AlarmActions' => [$snsTopic['TopicArn']],
            'OKActions' => [$snsTopic['TopicArn']],
            ...Aws::tags(),
        ]);

        return StepResult::SYNCED;
    }
}
