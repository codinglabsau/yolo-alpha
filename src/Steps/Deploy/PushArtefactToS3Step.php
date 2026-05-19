<?php

namespace Codinglabs\YoloAlpha\Steps\Deploy;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Illuminate\Filesystem\Filesystem;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class PushArtefactToS3Step implements Step
{
    public function __construct(
        protected string $environment,
        protected $filesystem = new Filesystem()
    ) {}

    public function __invoke(): StepResult
    {
        $appVersion = $this->filesystem->get(Paths::version());

        Aws::s3()->putObject([
            'Body' => file_get_contents(Paths::artefact()),
            'Bucket' => Paths::s3ArtefactsBucket(),
            'Key' => Paths::s3Artefacts($appVersion, Helpers::artefactName()),
        ]);

        return StepResult::SUCCESS;
    }
}
