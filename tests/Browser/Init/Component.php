<?php

namespace Radio\Tests\Browser\Init;

use Radio\Radio;

class Component
{
    use Radio;

    public $message = 'Thanks for using Radio!';

    public function changeMessage()
    {
        $this->message = 'The data is changing, yay!';
    }
}
