<?php

namespace Radio\Tests\Browser;

use Illuminate\Routing\Router;
use ReflectionClass;
use Radio\RadioServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Dusk\Options;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            RadioServiceProvider::class,
        ];
    }

    protected function setUp(): void
    {
        if (isset($_SERVER['CI'])) {
            Options::withoutUI();
        }

        parent::setUp();

        $this->tweakApplication(function ($app) {
            $app['view']->replaceNamespace('browser', __DIR__);

            require_once __DIR__ . '/routes.php';
        });
    }

    protected function tearDown(): void
    {
        $this->removeApplicationTweaks();

        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.debug', true);
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
        $app['config']->set('database.default', 'sqlite');
    }
}
