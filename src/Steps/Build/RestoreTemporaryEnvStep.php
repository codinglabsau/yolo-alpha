<?php

namespace Codinglabs\YoloAlpha\Steps\Build;

use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Illuminate\Filesystem\Filesystem;
use Codinglabs\YoloAlpha\Contracts\Step;

class RestoreTemporaryEnvStep implements Step
{
    public function __construct(
        protected string $environment,
        protected $filesystem = new Filesystem()
    ) {}

    public function __invoke(): void
    {
        // Once the build process is complete, move the .env into it's
        // final place to be added to the build artefact for deploy.
        $this->filesystem->move(
            Paths::build(Helpers::envFileName($this->environment) . '.tmp'),
            Paths::build('.env'),
        );
    }
}
