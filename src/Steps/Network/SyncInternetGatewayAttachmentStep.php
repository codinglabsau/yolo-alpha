<?php

namespace Codinglabs\YoloAlpha\Steps\Network;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\AwsResources;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

class SyncInternetGatewayAttachmentStep implements Step
{
    public function __invoke(array $options): StepResult
    {
        try {
            $vpc = AwsResources::vpc();
            $internetGateway = AwsResources::internetGateway();

            if (count($internetGateway['Attachments']) === 1
                && $internetGateway['Attachments'][0]['VpcId'] === $vpc['VpcId']
                && $internetGateway['Attachments'][0]['State'] === 'available') {
                return StepResult::SYNCED;
            }

            throw new ResourceDoesNotExistException('Could not find Internet Gateway Attachment');
        } catch (ResourceDoesNotExistException $e) {
            if (! Arr::get($options, 'dry-run')) {
                $vpc = AwsResources::vpc();
                $internetGateway = AwsResources::internetGateway();

                Aws::ec2()->attachInternetGateway([
                    'InternetGatewayId' => $internetGateway['InternetGatewayId'],
                    'VpcId' => $vpc['VpcId'],
                ]);

                return StepResult::CREATED;
            }

            return StepResult::WOULD_CREATE;
        }
    }
}
