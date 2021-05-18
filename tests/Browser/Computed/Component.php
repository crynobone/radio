<?php

namespace Radio\Tests\Browser\Computed;

use Radio\Attributes\Computed;
use Radio\Radio;

class Component
{
    use Radio;

    public $count = 1;

    #[Computed('getMessage')]
    public $message;

    public function increment()
    {
        $this->count++;
    }

    public function getMessage()
    {
        return 'You can count to ' . $this->count . '!';
    }
}
