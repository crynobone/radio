<?php

namespace Aerial\Tests\Browser;

use Aerial\AerialServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use ReflectionClass;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            AerialServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $reflection = new ReflectionClass(static::class);
        $folder = dirname($reflection->getFileName());

        if (file_exists($routes = $folder . '/routes.php')) {
            require_once $routes;
        }

        View::replaceNamespace('browser', __DIR__);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
    }
}
