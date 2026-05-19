<?php

namespace Codinglabs\YoloAlpha\Steps\Build;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;
use Codinglabs\YoloAlpha\Contracts\RunsOnBuild;

class ExecuteBuildStepsStep implements HasSubSteps, RunsOnBuild
{
    public function __invoke(): array
    {
        return Manifest::get('build');
    }
}
