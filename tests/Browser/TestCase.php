<?php

namespace Aerial\Tests\Browser;

use Aerial\AerialServiceProvider;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            AerialServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $this->app['config']->set('database.default', 'sqlite');
    }
}
