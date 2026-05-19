<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Aws;
use Codinglabs\YoloAlpha\Helpers;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\Rds;
use Codinglabs\YoloAlpha\Exceptions\ResourceDoesNotExistException;

trait UsesRds
{
    public static function dbSubnetGroup(): array
    {
        $name = Manifest::has('aws.rds.subnet')
            ? Manifest::get('aws.rds.subnet')
            : Helpers::keyedResourceName(Rds::PUBLIC_SUBNET_GROUP);

        $dbSubnetGroups = Aws::rds()->describeDBSubnetGroups();

        foreach ($dbSubnetGroups['DBSubnetGroups'] as $dbSubnetGroup) {
            if ($dbSubnetGroup['DBSubnetGroupName'] === $name) {
                return $dbSubnetGroup;
            }
        }

        throw new ResourceDoesNotExistException("Could not find RDS Subnet Group with name $name");
    }
}
