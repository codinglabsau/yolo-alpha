<?php

namespace Codinglabs\YoloAlpha\Steps\Storage;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Paths;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncS3BucketStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        $bucketName = Paths::s3AppBucket();

        try {
            AwsResources::bucket($bucketName);

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::s3()->createBucket([
                    'Bucket' => $bucketName,
                ]);

                Aws::s3()->waitUntil('BucketExists', [
                    'Bucket' => $bucketName,
                ]);

                Aws::s3()->putBucketTagging([
                    'Bucket' => $bucketName,
                    'Tagging' => [
                        ...Aws::tags([
                            'Name' => $bucketName,
                        ], wrap: 'TagSet'),
                    ],
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
