<?php

namespace Codinglabs\YoloAlpha\Steps\Deploy;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Manifest;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Concerns\SyncsRecordSets;
use Codinglabs\YoloAlpha\Contracts\ExecutesStandaloneStep;

class SyncStandaloneRecordSetStep implements ExecutesStandaloneStep
{
    use SyncsRecordSets;

    public function __invoke(array $options): StepResult
    {
        if (! Arr::get($options, 'dry-run')) {
            $this->syncRecordSet(
                apex: Manifest::apex(),
                domain: Manifest::get('domain'),
            );

            return StepResult::SYNCED;
        }

        return StepResult::WOULD_SYNC;
    }
}
