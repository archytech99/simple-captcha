<?php

namespace Archytech\Captcha;

use Illuminate\Support\ServiceProvider;

class SimpleCapthaServiceProvider extends ServiceProvider
{
    public function register()
    {
        /* Merge configuration files */
        if (file_exists(config_path('captcha.php'))) {
            $this->mergeConfigFrom(
                config_path('captcha.php'),
                'captcha'
            );
        }

        /* Bind captcha */
        $this->app->bind('captcha', function ($app) {
            return new SimpleCaptcha(
                $app['Illuminate\Session\Store']
            );
        });
    }

    public function boot()
    {
        /* Publish configuration files */
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->getConfigFile() => config_path('captcha.php'),
            ], 'config');
        }

        /* @var Router $router */
        $router = $this->app['router'];
        if ((float)$this->app->version() >= 5.2) {
            $router->get('captcha', '\Archytech\Captcha\Controller@getCaptcha')->middleware('web');
            $router->get('captcha/api', '\Archytech\Captcha\Controller@getCaptchaApi')->middleware('web');
            $router->any('captcha/test', '\Archytech\Captcha\Controller@testCaptcha')->middleware('web');
        } else {
            $router->get('captcha', '\Archytech\Captcha\Controller@getCaptcha');
            $router->get('captcha/api', '\Archytech\Captcha\Controller@getCaptchaApi');
            $router->any('captcha/test', '\Archytech\Captcha\Controller@testCaptcha');
        }

        /* @var Factory $validator */
        $validator = $this->app['validator'];

        /* Validator extensions */
        $validator->extend('captcha', function ($attribute, $value, $parameters) {
            return captcha_check($value);
        });

        /* Validator extensions */
        $validator->extend('captcha_api', function ($attribute, $value, $parameters) {
            return captcha_api_check($value, $parameters[0]);
        });
    }

    private function getConfigFile(): string
    {
        /* __DIR__.'/../config/captcha.php' */
        return $this->getPathPackage('config/captcha.php');
    }

    private function getPathPackage($path = 'src/'): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $path;
    }
}
