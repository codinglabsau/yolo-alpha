<?php

namespace Codinglabs\YoloAlpha\Steps\Image;

use Carbon\Carbon;
use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Contracts\Step;
use Codinglabs\YoloAlpha\Concerns\UsesEc2;

class CreateAmiStep implements Step
{
    use UsesEc2;

    public function __invoke(): string
    {
        $ami = Aws::ec2()->createImage([
            'InstanceId' => Helpers::app('amiInstanceId'),
            'Name' => Helpers::keyedResourceName(Carbon::now(Manifest::timezone())->format('y.W.N.Hi'), exclusive: false),
            'TagSpecifications' => [
                [
                    'ResourceType' => 'image',
                    ...Aws::tags(),
                ],
            ],
        ]);

        while (true) {
            // wait for AMI to be available
            $ami = Aws::ec2()->describeImages([
                'ImageIds' => [$ami['ImageId']],
            ])['Images'][0];

            if ($ami['State'] === 'available') {
                break;
            }

            sleep(3);
        }

        return $ami['ImageId'];
    }
}
