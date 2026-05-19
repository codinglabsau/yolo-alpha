<?php

namespace Codinglabs\YoloAlpha;

use Codinglabs\YoloAlpha\Concerns\UsesS3;
use Codinglabs\YoloAlpha\Concerns\UsesEc2;
use Codinglabs\YoloAlpha\Concerns\UsesIam;
use Codinglabs\YoloAlpha\Concerns\UsesRds;
use Codinglabs\YoloAlpha\Concerns\UsesSns;
use Codinglabs\YoloAlpha\Concerns\UsesSqs;
use Codinglabs\YoloAlpha\Concerns\UsesSsm;
use Codinglabs\YoloAlpha\Concerns\UsesRoute53;
use Codinglabs\YoloAlpha\Concerns\UsesCloudWatch;
use Codinglabs\YoloAlpha\Concerns\UsesCodeDeploy;
use Codinglabs\YoloAlpha\Concerns\UsesAutoscaling;
use Codinglabs\YoloAlpha\Concerns\UsesEventBridge;
use Codinglabs\YoloAlpha\Concerns\UsesCloudWatchLogs;
use Codinglabs\YoloAlpha\Concerns\UsesCertificateManager;
use Codinglabs\YoloAlpha\Concerns\UsesElasticLoadBalancingV2;

class AwsResources
{
    use UsesAutoscaling;
    use UsesCertificateManager;
    use UsesCloudWatch;
    use UsesCloudWatchLogs;
    use UsesCodeDeploy;
    use UsesEc2;
    use UsesElasticLoadBalancingV2;
    use UsesEventBridge;
    use UsesIam;
    use UsesRds;
    use UsesRoute53;
    use UsesS3;
    use UsesSns;
    use UsesSqs;
    use UsesSsm;
}
