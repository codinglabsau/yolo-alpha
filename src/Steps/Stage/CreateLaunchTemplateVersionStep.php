<?php

namespace Codinglabs\YoloAlpha\Steps\Stage;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class CreateLaunchTemplateVersionStep implements Step
{
    public function __invoke(array $options): string|StepResult
    {
        if (! Arr::get($options, 'dry-run')) {
            $launchTemplate = AwsResources::launchTemplate();

            $launchTemplateVersion = Aws::ec2()->createLaunchTemplateVersion([
                'LaunchTemplateId' => $launchTemplate['LaunchTemplateId'],
                'LaunchTemplateData' => [
                    ...AwsResources::launchTemplatePayload()['LaunchTemplateData'],
                    'ImageId' => $options['ami-id'],
                ],
            ])['LaunchTemplateVersion'];

            // set the updated version as the default
            Aws::ec2()->modifyLaunchTemplate([
                'LaunchTemplateId' => $launchTemplate['LaunchTemplateId'],
                'DefaultVersion' => $launchTemplateVersion['VersionNumber'],
            ]);

            return sprintf('version %s', $launchTemplateVersion['VersionNumber']);
        }

        return StepResult::WOULD_CREATE;
    }
}
