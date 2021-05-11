<?php

declare(strict_types = 1);

namespace Aerial;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class AerialServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('aerial')
            ->hasAssets()
            ->hasRoute('web');
    }

    public function packageBooted(): void
    {
        $this->registerBladeDirectives();
    }

    public function registerBladeDirectives(): static
    {
        Blade::directive('aerial', function (string $expression) {
            return "<?php \Aerial\aerial({$expression}); ?>";
        });

        Blade::directive('aerialScripts', function () {
            return sprintf(
                '<script src="%s" data-token="%s"></script>',
                mix(
                    'aerial.js',
                    'vendor/aerial',
                ),
                csrf_token(),
            );
        });

        return $this;
    }
}
