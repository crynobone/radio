<?php

namespace Radio\Tests\Browser;

use Orchestra\Testbench\Dusk\Options;
use Orchestra\Testbench\Dusk\TestCase as BaseTestCase;
use Radio\RadioServiceProvider;
use ReflectionClass;

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
            $reflection = new ReflectionClass(static::class);
            $folder = dirname($reflection->getFileName());

            if (file_exists($routes = $folder . '/routes.php')) {
                $app['router']->middleware('web')->group($routes);
            }

            $app['view']->replaceNamespace('browser', __DIR__);
        });
    }

    protected function tearDown(): void
    {
        $this->removeApplicationTweaks();

        parent::tearDown();
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');
        $app['config']->set('database.default', 'sqlite');
    }
}
