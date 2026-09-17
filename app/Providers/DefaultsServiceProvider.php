<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Illuminate\Validation\Rules\Password;
use Laravel\Head\Enums\ImageType;
use Laravel\Head\Enums\OgType;
use Laravel\Head\Facades\Head;
use Laravel\Head\HeadBuilder;

final class DefaultsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureModels();
        $this->configureDates();
        $this->configureCommands();
        $this->configureUrls();
        $this->configureVite();
        $this->configurePasswords();
        $this->configureHead();
        $this->configureTests();
    }

    private function configureModels(): void
    {
        Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();
    }

    private function configureDates(): void
    {
        Date::use(CarbonImmutable::class);
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands(
            $this->app->isProduction(),
        );
    }

    private function configureUrls(): void
    {
        if ($this->app->isProduction()) {
            URL::forceHttps();
        }
    }

    private function configureVite(): void
    {
        Vite::useAggressivePrefetching();
    }

    private function configurePasswords(): void
    {
        Password::defaults(fn (): ?Password => $this->app->isProduction()
            ? Password::min(12)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->max(255)
                ->uncompromised()
            : null,
        );
    }

    private function configureHead(): void
    {
        Head::defaults(function (HeadBuilder $head): void {
            $appName = config('app.name');

            $head
                ->title($appName, suffix: ' - '.$appName)
                ->canonical()
                ->viewport('width=device-width, initial-scale=1')
                ->og(siteName: $appName, type: OgType::Website)
                ->searchableByRobots()
                ->favicon('/favicon.ico')
                ->favicon('/favicon.svg', type: ImageType::Svg)
                ->appleTouchIcon('/apple-touch-icon.png');
        });
    }

    private function configureTests(): void
    {
        if (! $this->app->runningUnitTests()) {
            return;
        }

        Http::preventStrayRequests();
        Sleep::fake();
    }
}
