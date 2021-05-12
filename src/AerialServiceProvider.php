<?php

declare(strict_types = 1);

namespace Radio;

use Illuminate\Support\Facades\Blade;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class RadioServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('radio')
            ->hasAssets()
            ->hasRoute('web')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->registerBladeDirectives();
    }

    public function registerBladeDirectives(): static
    {
        Blade::directive('radio', function (string $expression) {
            return "<?php \Radio\radio({$expression}); ?>";
        });

        Blade::directive('radioScripts', function () {
            return sprintf(
                '<script src="%s" data-token="%s"></script>',
                mix(
                    'radio.js',
                    'vendor/radio',
                ),
                csrf_token(),
            );
        });

        return $this;
    }
}
