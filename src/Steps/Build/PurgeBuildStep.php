<?php

namespace Codinglabs\YoloAlpha\Steps\Build;

use Codinglabs\YoloAlpha\Paths;
use Illuminate\Filesystem\Filesystem;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;

class PurgeBuildStep implements Step
{
    public function __construct(
        protected string $environment,
        protected $filesystem = new Filesystem()
    ) {}

    public function __invoke(): StepResult
    {
        $this->filesystem->deleteDirectory(Paths::yolo());

        return StepResult::SUCCESS;
    }
}
