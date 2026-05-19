<?php

namespace Codinglabs\YoloAlpha\Steps\Start\Web;

use GuzzleHttp\Client;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Contracts\RunsOnAwsWeb;

class WarmMultitenantedApplicationStep extends TenantStep implements RunsOnAwsWeb
{
    public function __invoke(array $options): StepResult
    {
        // make a request to each tenant index to warm the cacheable things
        (new Client(['timeout' => 10]))
            ->get('localhost', [
                'headers' => [
                    'Host' => $this->config()['domain'],
                    'X-Forwarded-Proto' => 'https',
                    'User-Agent' => 'YOLO-Warmer/1.0',
                ],
            ])
            ->getBody();

        return StepResult::SUCCESS;
    }
}
