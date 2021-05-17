<?php

declare(strict_types = 1);

namespace Radio\Concerns;

trait WithEvents
{
    protected array $radioEventQueue = [];

    protected function clearRadioEventQueue(): void
    {
        $this->radioEventQueue = [];
    }

    public function dehydrateRadioEvents(): array
    {
        try {
            return ['events' => $this->radioEventQueue];
        } finally {
            $this->clearRadioEventQueue();
        }
    }

    protected function dispatchEvent(string $name, $data = null)
    {
        $this->radioEventQueue[] = [
            'name' => $name,
            'data' => $data,
        ];
    }
}