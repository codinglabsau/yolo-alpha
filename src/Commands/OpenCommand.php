<?php

namespace Codinglabs\YoloAlpha\Commands;

use Codinglabs\YoloAlpha\TUI\Dashboard;

class OpenCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->setName('open')
            ->setDescription('Open the YOLO TUI application');
    }

    public function handle(): void
    {
        (new Dashboard())->prompt();
    }
}
