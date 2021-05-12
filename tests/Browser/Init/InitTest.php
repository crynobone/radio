<?php

namespace Radio\Tests\Browser\Init;

use Laravel\Dusk\Browser;
use Radio\Tests\Browser\TestCase;

class InitTest extends TestCase
{
    public function test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/browser/init');

            $browser->assertSee('Thanks for using Radio!');
        });
    }
}
