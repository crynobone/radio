<?php

declare(strict_types = 1);

namespace Radio;

use Exception;
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
            return "<?php \Radio\\radio({$expression}); ?>";
        });

        Blade::directive('radioScripts', function () {
            try {
                $script = mix('radio.js', 'vendor/radio');
            } catch (Exception $e) {
                $script = route('radio.scripts', ['path' => 'radio.js']);
            }

            return sprintf(
                '<script src="%s" data-token="%s"></script>',
                $script,
                csrf_token(),
            );
        });

        return $this;
    }
}
