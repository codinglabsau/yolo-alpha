<?php

namespace Codinglabs\YoloAlpha\Concerns;

use Codinglabs\YoloAlpha\Manifest;

trait ResolvesDatabases
{
    protected function databases(): array
    {
        return Manifest::isMultitenanted()
            ? [
                env('DB_DATABASE'), // landlord
                ...array_keys(Manifest::tenants()),
            ]
            : [env('DB_DATABASE')];
    }
}
