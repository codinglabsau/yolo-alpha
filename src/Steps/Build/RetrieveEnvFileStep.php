<?php

namespace Codinglabs\YoloAlpha\Steps\Build;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Contracts\Step;

class RetrieveEnvFileStep implements Step
{
    public function __invoke(array $options = []): void
    {
        $filename = sprintf('.env.%s', Helpers::environment());
        $path = array_key_exists('save-as', $options)
            ? $options['save-as']
            : Paths::base($filename);

        Aws::s3()->getObject([
            'Bucket' => Paths::s3ArtefactsBucket(),
            'Key' => $filename,
            'SaveAs' => $path,
        ]);
    }
}
