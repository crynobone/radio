<?php

namespace Aerial\Tests\Browser\Init;

use Laravel\Dusk\Browser;
use Aerial\Tests\Browser\TestCase;

class InitTest extends TestCase
{
    public function test()
    {
        $this->browse(function (Browser $browser) {
            $browser->visitRoute('browser.init');

            $browser->assertSee('Thanks for using Aerial!');
        });
    }
}
