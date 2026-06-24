<?php

namespace Codinglabs\YoloAlpha\Steps\Build;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Illuminate\Filesystem\Filesystem;
use Codinglabs\YoloAlpha\Contracts\Step;

class CreateTemporaryEnvStep implements Step
{
    public function __construct(
        protected string $environment,
        protected $filesystem = new Filesystem()
    ) {}

    public function __invoke(): void
    {
        // Rename the AWS .env file temporarily to prevent composer and artisan
        // commands using values within commands. This could lead to bad things.
        $this->filesystem->move(
            Paths::build(Helpers::envFileName($this->environment)),
            Paths::build(Helpers::envFileName($this->environment) . '.tmp'),
        );
    }
}
