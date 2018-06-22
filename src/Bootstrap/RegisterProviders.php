<?php

declare(strict_types=1);

/**
 * This file is part of Laravel Zero.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace LaravelZero\Framework\Bootstrap;

use function collect;
use LaravelZero\Framework\Providers;
use LaravelZero\Framework\Components;
use Illuminate\Contracts\Foundation\Application;
use LaravelZero\Framework\Contracts\BoostrapperContract;
use NunoMaduro\LaravelConsoleMenu\LaravelConsoleMenuServiceProvider;
use NunoMaduro\LaravelConsoleTask\LaravelConsoleTaskServiceProvider;
use NunoMaduro\LaravelConsoleSummary\LaravelConsoleSummaryServiceProvider;
use NunoMaduro\LaravelDesktopNotifier\LaravelDesktopNotifierServiceProvider;
use Illuminate\Foundation\Bootstrap\RegisterProviders as BaseRegisterProviders;

class RegisterProviders extends BaseRegisterProviders implements BoostrapperContract
{
    /**
     * Core providers.
     *
     * @var string[]
     */
    protected $providers = [
        Providers\Cache\CacheServiceProvider::class,
        Providers\Filesystem\FilesystemServiceProvider::class,
        Providers\Composer\ComposerServiceProvider::class,
        LaravelDesktopNotifierServiceProvider::class,
        LaravelConsoleTaskServiceProvider::class,
        LaravelConsoleMenuServiceProvider::class,
        LaravelConsoleSummaryServiceProvider::class,
    ];

    /**
     * Optional components.
     *
     * @var string[]
     */
    protected $components = [
        Components\Log\Provider::class,
        Components\Database\Provider::class,
        Components\ConsoleDusk\Provider::class,
    ];

    /**
     * Bootstrap register providers.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function bootstrap(Application $app): void
    {
        /*
         * First, we register Laravel Foundation providers.
         */
        parent::bootstrap($app);

        /*
         * Then we register Laravel Zero available providers.
         */
        collect($this->providers)
            ->merge(
                collect($this->components)->filter(
                    function ($component) use ($app) {
                        return (new $component($app))->isAvailable();
                    }
                )
            )
            ->each(
                function ($serviceProviderClass) use ($app) {
                    $app->register($serviceProviderClass);
                }
            );
    }
}
