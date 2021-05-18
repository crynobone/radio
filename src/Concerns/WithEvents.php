<?php

declare(strict_types = 1);

namespace Radio\Concerns;

trait WithEvents
{
    protected array $radioEventQueue = [];

    protected function dispatchEvent(string $name, $data = null): void
    {
        $this->radioEventQueue[] = [
            'name' => $name,
            'data' => $data,
        ];
    }

    public function dehydrateRadioEvents(): array
    {
        try {
            return ['events' => $this->radioEventQueue];
        } finally {
            $this->clearRadioEventQueue();
        }
    }

    protected function clearRadioEventQueue(): void
    {
        $this->radioEventQueue = [];
    }
}