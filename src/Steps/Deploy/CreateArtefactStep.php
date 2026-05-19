<?php

namespace Codinglabs\YoloAlpha\Steps\Deploy;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Symfony\Component\Process\Process;

class CreateArtefactStep implements Step
{
    public function __invoke(): StepResult
    {
        (Process::fromShellCommandline(
            command: sprintf('tar czf ../%s * .??*', Helpers::artefactName()),
            cwd: Paths::build(),
            env: [],
            timeout: null
        ))->mustRun();

        return StepResult::SUCCESS;
    }
}
