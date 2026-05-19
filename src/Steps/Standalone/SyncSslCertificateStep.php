<?php

namespace Codinglabs\YoloAlpha\Steps\Standalone;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\ExecutesDomainStep;
use Codinglabs\YoloAlpha\Concerns\SyncsSslCertificates;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncSslCertificateStep implements ExecutesDomainStep
{
    use SyncsSslCertificates;

    public function __invoke(array $options): StepResult
    {
        try {
            $certificate = AwsResources::certificate(Manifest::apex());

            if ($certificate['Status'] === 'PENDING_VALIDATION') {
                if (! Arr::get($options, 'dry-run')) {
                    $this->validateCertificate($certificate['CertificateArn'], Manifest::apex());
                } else {
                    return StepResult::WOULD_SYNC;
                }
            }

            return StepResult::SYNCED;
        } catch (ResourceDoesNotExistException) {
            if (! Arr::get($options, 'dry-run')) {
                $this->requestCertificate(Manifest::apex());

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
