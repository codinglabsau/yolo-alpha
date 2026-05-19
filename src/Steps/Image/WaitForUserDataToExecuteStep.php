<?php

namespace Codinglabs\YoloAlpha\Steps\Image;

use Codinglabs\YoloAlpha\Helpers;
use Symfony\Component\Process\Process;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\FormatsSshCommands;

class WaitForUserDataToExecuteStep implements Step
{
    use FormatsSshCommands;

    public function __invoke(): StepResult
    {
        while (true) {
            $finished = Process::fromShellCommandline(
                command: static::formatSshCommand(Helpers::app('amiIp')) . ' "test -f /home/ubuntu/finished"',
            )->run();

            if ($finished === 0) {
                break;
            }

            sleep(3);
        }

        return StepResult::SUCCESS;
    }
}
