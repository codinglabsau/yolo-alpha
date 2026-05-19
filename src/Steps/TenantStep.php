<?php

namespace Codinglabs\YoloAlpha\Steps;

use Codinglabs\YoloAlpha\Enums\StepResult;
use Codinglabs\YoloAlpha\Contracts\ExecutesTenantStep;

abstract class TenantStep implements ExecutesTenantStep
{
    protected string $tenantId;

    protected array $config;

    abstract public function __invoke(array $options): StepResult;

    public function tenantId(): string
    {
        return $this->tenantId;
    }

    public function config(): array
    {
        return $this->config;
    }

    public function setTenantId(string $tenantId): self
    {
        $this->tenantId = $tenantId;

        return $this;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;

        return $this;
    }
}
