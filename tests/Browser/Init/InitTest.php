<?php

namespace Radio\Tests\Browser\Init;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Laravel\Dusk\Browser;
use Radio\Tests\Browser\TestCase;

class InitTest extends TestCase
{
    public function test()
    {
        $this->browse(function (Browser $browser) {
            $browser
                ->visit('/browser/init')
                ->waitForText('Thanks for using Radio!')
                ->assertSee('Thanks for using Radio!');

            $browser
                ->click('@change-message')
                ->waitForText('The data is changing, yay!')
                ->assertSee('The data is changing, yay!');
        });
    }
}
