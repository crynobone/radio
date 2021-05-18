<?php

namespace Radio\Tests\Browser\Computed;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Laravel\Dusk\Browser;
use Radio\Tests\Browser\TestCase;

class ComputedTest extends TestCase
{
    public function test()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/browser/computed')
                ->waitForText('You can count to 1!')
                ->assertSee('You can count to 1!');

            $browser
                ->click('@increment')
                ->waitForText('You can count to 2!')
                ->assertSee('You can count to 2!');
        });
    }
}
