<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Web;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;

class SyncOctaneStep implements RunsOnAwsWeb
{
    public function __invoke(array $options): StepResult
    {
        $file = sprintf('/etc/supervisor/conf.d/%s', Helpers::keyedResourceName('octane.conf'));

        if (! Manifest::get('aws.ec2.octane')) {
            if (file_exists($file)) {
                unlink($file);
            }

            return StepResult::SKIPPED;
        }

        file_put_contents(
            $file,
            str_replace(
                search: [
                    '{NAME}',
                ],
                replace: [
                    Manifest::name(),
                ],
                subject: file_get_contents(Paths::stubs('supervisor/octane.conf.stub'))
            )
        );

        return StepResult::SYNCED;
    }
}
