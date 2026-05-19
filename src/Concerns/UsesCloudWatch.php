<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

trait UsesCloudWatch
{
    public static function alarm(string $alarmName): array
    {
        $alarms = Aws::cloudWatch()->describeAlarms();

        foreach ($alarms['MetricAlarms'] as $alarm) {
            if ($alarm['AlarmName'] === $alarmName) {
                return $alarm;
            }
        }

        throw new ResourceDoesNotExistException("Could not find alarm with name $alarmName");
    }
}
