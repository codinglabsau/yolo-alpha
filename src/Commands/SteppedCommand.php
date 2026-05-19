<?php

namespace Codinglabs\YoloAlpha\Commands;

use Codinglabs\YoloAlpha\Concerns\RunsSteppedCommands;

use function Laravel\Prompts\info;
use function Laravel\Prompts\intro;

abstract class SteppedCommand extends Command
{
    use RunsSteppedCommands;

    protected array $steps;

    public function handle(): int
    {
        $environment = $this->argument('environment');

        intro(sprintf('Executing %s steps in %s', $this->getName(), $environment));

        $totalTime = $this->handleSteps($environment);

        if (! $this->option('no-progress')) {
            info(sprintf('Completed successfully in %ss.', $totalTime));
        }

        return self::SUCCESS;
    }
}
