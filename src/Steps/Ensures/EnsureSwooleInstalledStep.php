<?php

namespace Codinglabs\YoloAlpha\Steps\Ensures;

use Illuminate\Support\Str;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Contracts\Step;
use Symfony\Component\Process\Process;
use Codinglabs\YoloAlpha\Concerns\FormatsSshCommands;

class EnsureSwooleInstalledStep implements Step
{
    use FormatsSshCommands;

    public function __invoke(): string
    {
        return Str::of(
            Process::fromShellCommandline(
                command: static::formatSshCommand(
                    ipAddress: Helpers::app('amiIp'),
                    command: 'php -m | grep swoole'
                )
            )->mustRun()
                ->getOutput()
        )
            ->trim()
            ->wrap(before: '<info>', after: '</info>');
    }
}
