<?php

namespace Codinglabs\YoloAlpha\Steps\Deploy;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Illuminate\Filesystem\Filesystem;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Enums\ServerGroup;
use Codinglabs\YoloAlpha\Concerns\UsesCodeDeploy;
use Codinglabs\YoloAlpha\Concerns\ParsesOnlyOption;

class CreateCodeDeployDeploymentsStep implements Step
{
    use ParsesOnlyOption;
    use UsesCodeDeploy;

    public function __construct(protected string $environment, protected $filesystem = new Filesystem()) {}

    public function __invoke(array $options): StepResult
    {
        $appVersion = $this->filesystem->get(Paths::version());

        if ($this->shouldRunOnGroup(ServerGroup::SCHEDULER, $options)) {
            $this->createSchedulerServerDeployment($appVersion);
        }

        if ($this->shouldRunOnGroup(ServerGroup::QUEUE, $options)) {
            $this->createQueueServerDeployment($appVersion);
        }

        if ($this->shouldRunOnGroup(ServerGroup::WEB, $options)) {
            $this->createWebServerDeployment($appVersion);
        }

        return StepResult::SUCCESS;
    }

    protected function createSchedulerServerDeployment(string $appVersion): void
    {
        Aws::codeDeploy()->createDeployment([
            ...static::deploymentPayload($appVersion),
            ...[
                'deploymentGroupName' => Helpers::keyedResourceName(ServerGroup::SCHEDULER),
            ],
        ]);
    }

    protected function createQueueServerDeployment(string $appVersion): void
    {
        Aws::codeDeploy()->createDeployment([
            ...static::deploymentPayload($appVersion),
            ...[
                'deploymentGroupName' => Helpers::keyedResourceName(ServerGroup::QUEUE),
            ],
        ]);
    }

    protected function createWebServerDeployment(string $appVersion): void
    {
        Aws::codeDeploy()->createDeployment([
            ...static::deploymentPayload($appVersion),
            ...[
                'deploymentGroupName' => Helpers::keyedResourceName(ServerGroup::WEB),
            ],
        ]);
    }

    protected static function deploymentPayload(string $appVersion): array
    {
        return [
            'applicationName' => static::applicationName(),
            'description' => sprintf('Version %s deployed by %s', $appVersion, get_current_user()),
            'revision' => [
                'revisionType' => 'S3',
                's3Location' => [
                    'bucket' => Paths::s3ArtefactsBucket(),
                    'bundleType' => 'tgz',
                    'key' => Paths::s3Artefacts($appVersion, Helpers::artefactName()),
                ],
            ],
        ];
    }
}
