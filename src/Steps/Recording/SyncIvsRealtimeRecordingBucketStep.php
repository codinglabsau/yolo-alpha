<?php

namespace Codinglabs\YoloAlpha\Steps\Recording;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\Iam;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

use function Laravel\Prompts\note;

class SyncIvsRealtimeRecordingBucketStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        if (! Manifest::ivsRealtimeRecordingEnabled()) {
            return StepResult::SKIPPED;
        }

        $bucket = self::bucketName();

        try {
            AwsResources::bucket($bucket);

            if (! Arr::get($options, 'dry-run')) {
                $this->putBucketPolicy($bucket);
            }

            note(sprintf('IVS recordings bucket: %s', $bucket));
            note(sprintf('Set AWS_IVS_REALTIME_RECORDINGS_BUCKET=%s', $bucket));

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                Aws::s3()->createBucket(['Bucket' => $bucket]);
                Aws::s3()->waitUntil('BucketExists', ['Bucket' => $bucket]);
                Aws::s3()->putBucketTagging([
                    'Bucket' => $bucket,
                    'Tagging' => [...Aws::tags(['Name' => $bucket], wrap: 'TagSet')],
                ]);
                $this->putBucketPolicy($bucket);

                note(sprintf('IVS recordings bucket: %s', $bucket));
                note(sprintf('Set AWS_IVS_REALTIME_RECORDINGS_BUCKET=%s', $bucket));

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }

    protected function putBucketPolicy(string $bucket): void
    {
        $region = Manifest::get('aws.region');
        $accountId = Aws::accountId();
        $mediaConvertRoleName = Helpers::keyedResourceName(Iam::MEDIA_CONVERT_ROLE);
        $mediaConvertRoleArn = "arn:aws:iam::{$accountId}:role/{$mediaConvertRoleName}";

        // IVS Real-Time writes via ivs-composite.{region}.amazonaws.com, not ivs.amazonaws.com.
        // It requires PutObjectAcl with bucket-owner-full-control so ownership transfers to the
        // bucket owner, allowing same-account services (e.g. MediaConvert) to read the objects.
        Aws::s3()->putBucketPolicy([
            'Bucket' => $bucket,
            'Policy' => json_encode([
                'Version' => '2012-10-17',
                'Statement' => [
                    [
                        'Sid' => 'IVSRealtimeRecording',
                        'Effect' => 'Allow',
                        'Principal' => ['Service' => "ivs-composite.{$region}.amazonaws.com"],
                        'Action' => ['s3:PutObject', 's3:PutObjectAcl'],
                        'Resource' => "arn:aws:s3:::{$bucket}/*",
                        'Condition' => [
                            'StringEquals' => ['s3:x-amz-acl' => 'bucket-owner-full-control'],
                            'Bool' => ['aws:SecureTransport' => 'true'],
                        ],
                    ],
                    [
                        'Sid' => 'MediaConvertRead',
                        'Effect' => 'Allow',
                        'Principal' => ['AWS' => $mediaConvertRoleArn],
                        'Action' => ['s3:GetObject', 's3:GetObjectVersion', 's3:ListBucket'],
                        'Resource' => ["arn:aws:s3:::{$bucket}", "arn:aws:s3:::{$bucket}/*"],
                    ],
                ],
            ]),
        ]);

        Aws::s3()->putBucketOwnershipControls([
            'Bucket' => $bucket,
            'OwnershipControls' => [
                'Rules' => [['ObjectOwnership' => 'BucketOwnerPreferred']],
            ],
        ]);
    }

    public static function bucketName(): string
    {
        return Helpers::keyedResourceName('ivs-realtime-recordings');
    }
}
