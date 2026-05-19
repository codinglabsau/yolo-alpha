<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

trait UsesCertificateManager
{
    public static function certificate(string $domain): array
    {
        $certificates = Aws::acm()->listCertificates();

        foreach ($certificates['CertificateSummaryList'] as $certificate) {
            if ($certificate['DomainName'] === $domain) {
                return $certificate;
            }
        }

        throw new ResourceDoesNotExistException("Could not find certificate for domain $domain");
    }
}
