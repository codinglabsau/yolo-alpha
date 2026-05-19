<?php

namespace Codinglabs\YoloAlpha\Steps\Deploy;

use Illuminate\Support\Arr;
use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Steps\TenantStep;
use Codinglabs\YoloAlpha\Concerns\SyncsRecordSets;

class SyncMultitenancyRecordSetStep extends TenantStep
{
    use SyncsRecordSets;

    public function __invoke(array $options): StepResult
    {
        if (! Arr::get($options, 'dry-run')) {
            $this->syncRecordSet(
                apex: $this->config['apex'],
                domain: $this->config['domain'],
            );

            return StepResult::SYNCED;
        }

        return StepResult::WOULD_SYNC;
    }
}
