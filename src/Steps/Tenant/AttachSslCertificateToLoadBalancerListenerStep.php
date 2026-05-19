<?php

namespace Codinglabs\YoloAlpha\Steps\Tenant;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class AttachSslCertificateToLoadBalancerListenerStep extends TenantStep
{
    public function __invoke(array $options): StepResult
    {
        if (Arr::get($options, 'dry-run')) {
            return StepResult::WOULD_SYNC;
        }

        $certificate = AwsResources::certificate($this->config['apex']);

        if ($certificate['Status'] !== 'ISSUED') {
            do {
                $certificate = AwsResources::certificate($this->config['apex']);

                // take a little snooze until the certificate is issued
                sleep(2);
            } while ($certificate['Status'] !== 'ISSUED');
        }

        try {
            AwsResources::listenerCertificate(
                AwsResources::loadBalancerListenerOnPort(443)['ListenerArn'],
                AwsResources::certificate($this->config['apex'])['CertificateArn']
            );
        } catch (ResourceDoesNotExistException) {
            Aws::elasticLoadBalancingV2()->addListenerCertificates([
                'Certificates' => [
                    [
                        'CertificateArn' => AwsResources::certificate($this->config['apex'])['CertificateArn'],
                    ],
                ],
                'ListenerArn' => AwsResources::loadBalancerListenerOnPort(443)['ListenerArn'],
            ]);
        }

        return StepResult::SYNCED;
    }
}
