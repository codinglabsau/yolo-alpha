<?php

namespace Codinglabs\YoloAlpha\Contracts;

interface HasSubSteps extends Step
{
    public function __invoke(): array;
}
