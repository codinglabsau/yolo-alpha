<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Illuminate\Support\Str;
use Codinglabs\YoloAlpha\Helpers;
use Symfony\Component\Process\Process;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Concerns\FormatsSshCommands;

class EnsureNodeInstalledStep implements Step
{
    use FormatsSshCommands;

    public function __invoke(): string
    {
        return Str::of(Process::fromShellCommandline(
            command: static::formatSshCommand(
                ipAddress: Helpers::app('amiIp'),
                command: 'node -v'
            )
        )->mustRun()
            ->getOutput())
            ->trim()
            ->wrap(before: '<info>', after: '</info>');
    }
}
