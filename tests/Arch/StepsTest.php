<?php

arch()
    ->expect('Codinglabs\YoloAlpha\Steps')
    ->toBeInvokable()
    ->toHaveSuffix('Step');

arch()
    ->expect('Codinglabs\YoloAlpha\Steps\Start\All')
    ->toImplement('Codinglabs\YoloAlpha\Contracts\RunsOnAws');

arch()
    ->expect('Codinglabs\YoloAlpha\Steps\Start\Queue')
    ->toImplement('Codinglabs\YoloAlpha\Contracts\RunsOnAwsQueue');

arch()
    ->expect('Codinglabs\YoloAlpha\Steps\Start\RunsOnAwsScheduler')
    ->toImplement('Codinglabs\YoloAlpha\Contracts\RunsOnAws');

arch()
    ->expect('Codinglabs\YoloAlpha\Steps\Start\Web')
    ->toImplement('Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb');
