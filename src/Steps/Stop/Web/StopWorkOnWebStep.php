<?php

namespace Codinglabs\YoloAlpha\Steps\Stop\Web;

use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;
use Codinglabs\YoloAlpha\Concerns\InteractsWithNginx;
use Codinglabs\YoloAlpha\Concerns\InteractsWithSupervisor;

class StopWorkOnWebStep implements RunsOnAwsWeb
{
    use InteractsWithNginx;
    use InteractsWithSupervisor;

    public function __invoke(): StepResult
    {
        $this->stopSupervisorWorkers();
        $this->stopNginx();

        return StepResult::SUCCESS;
    }
}
