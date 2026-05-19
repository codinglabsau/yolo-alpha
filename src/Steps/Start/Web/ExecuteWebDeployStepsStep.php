<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Web;

use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\HasSubSteps;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;

class ExecuteWebDeployStepsStep implements HasSubSteps, RunsOnAwsWeb
{
    public function __invoke(): array
    {
        return Manifest::get('deploy-web', []);
    }
}
