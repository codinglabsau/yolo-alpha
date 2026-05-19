<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Commands\Command;
use Codinglabs\YoloAlpha\Contracts\RunsOnAws;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsScheduler;
use Codinglabs\YoloAlpha\Contracts\ExecutesStandaloneStep;
use Codinglabs\YoloAlpha\Contracts\ExecutesMultitenancyStep;

trait ChecksIfCommandsShouldBeRunning
{
    public function shouldBeRunning(Command|Step $instance): bool
    {
        if (
            $instance instanceof ExecutesStandaloneStep && Manifest::isMultitenanted()
            || $instance instanceof ExecutesMultitenancyStep && ! Manifest::isMultitenanted()) {
            return false;
        }

        if (Aws::runningInAws()) {
            if ($instance instanceof RunsOnAwsWeb) {
                return Aws::runningInAwsWebEnvironment();
            }

            if ($instance instanceof RunsOnAwsQueue) {
                return Aws::runningInAwsQueueEnvironment();
            }

            if ($instance instanceof RunsOnAwsScheduler) {
                return Aws::runningInAwsSchedulerEnvironment();
            }

            return $instance instanceof RunsOnAws;
        }

        return ! $instance instanceof RunsOnAws;
    }
}
