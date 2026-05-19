<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Web;

use GuzzleHttp\Client;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;
use Codinglabs\YoloAlpha\Contracts\ExecutesStandaloneStep;

class WarmApplicationStep implements ExecutesStandaloneStep, RunsOnAwsWeb
{
    public function __invoke(array $options): StepResult
    {
        // make a request to each tenant index to warm the cacheable things
        (new Client(['timeout' => 10]))
            ->get('localhost', [
                'headers' => [
                    'Host' => Manifest::get('domain'),
                    'X-Forwarded-Proto' => 'https',
                    'User-Agent' => 'YOLO-Warmer/1.0',
                ],
            ])
            ->getBody();

        return StepResult::SUCCESS;
    }
}
