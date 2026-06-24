<?php

namespace Codinglabs\YoloAlpha\Steps\Build;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Enums\Iam;
use Illuminate\Filesystem\Filesystem;
use Codinglabs\YoloAlpha\Contracts\Step;

class ConfigureEnvAndVersionStep implements Step
{
    public function __construct(
        protected string $environment,
        protected $filesystem = new Filesystem()
    ) {}

    public function __invoke(array $options): void
    {
        $appVersion = Arr::get($options, 'app-version');

        $this->filesystem->put(
            Paths::version(),
            $appVersion
        );

        $this->filesystem->append(
            Paths::build(Helpers::envFileName($this->environment)),
            $this->generateValues([
                'APP_VERSION' => $appVersion,
                'ASSET_URL' => Paths::assetUrl($appVersion),
                'AWS_MEDIACONVERT_ROLE_ID' => sprintf(
                    'arn:aws:iam::%s:role/%s',
                    Aws::accountId(),
                    Helpers::keyedResourceName(Iam::MEDIA_CONVERT_ROLE),
                ),
            ])
        );
    }

    protected function generateValues(array $values): string
    {
        $result = PHP_EOL . '# YOLO generated values' . PHP_EOL;

        foreach ($values as $key => $value) {
            $result .= "$key=$value" . PHP_EOL;
        }

        return $result;
    }
}
