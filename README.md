# A simple PHP CAPTCHA script

This code is the object oriented version of the original code [simple-captcha](https://github.com/yasirmturk/simple-php-captcha/). Minor improvements (dynamic path for fonts and background images).

## Requirements

- PHP `^7.2`
- Laravel Framework: `^6.0|^7.0`
- GD library,
- illuminate/config: `^6|^7`,
- illuminate/filesystem: `^6|^7`,
- illuminate/support: `^6|^7`,
- illuminate/hashing: `^6|^7`,
- illuminate/session: `^6|^7`,
- intervention/image: `~2.5`

## Installation

Require this package in the `composer.json` of your laravel project. This will download the requirements package:

```bash
composer require archytech/simple-captcha
```

Once Composer has installed or updated, you need to register Captcha. Open up `config/app.php` and:

- find the `providers` key and register the `SimpleCapthaServiceProvider`

    ```php
    'providers' => [
        /*
        * Package Service Providers ...
        */
        Archytech\Captcha\SimpleCapthaServiceProvider::class,
    ]
    ```

- find the `aliases` key and add `Archytech\Captcha\Facades\Facade` as `Captcha`

    ```php
    'aliases' => [
        ...
        'Captcha' => Archytech\Captcha\Facades\Facade::class,
    ]
    ```

Finally you need to publish a configuration file by running the following artisan command.

```bash
php artisan vendor:publish --provider="Archytech\Captcha\SimpleCapthaServiceProvider"
```

This will copy the configuration file to `config/captcha.php`

## Validation

- via controller

    ```php
    $validator = validator()->make(request()->all(), [
        'captcha' => 'required|captcha'
    ]);

    if ($validator->fails()) {
        // TODO if failed
    } else {
        // TODO if matched
    }
    ```

- via api

    ```php
    $validator = validator()->make(request()->all(), [
        'captcha' => 'required|captcha_api:' . request('key')
    ]);

    if ($validator->fails()) {
        return response()->json([
            'message' => 'Invalid captcha',
        ]);
    } else {
        // TODO if matched
    }
    ```

## Testing

- Once installation & configuration complete, you can access via `php artisan serve` and open url `http://localhost:8000/captcha/test`

## Get Image

- `captcha();`
- `Captcha::init();`

## Get Source Url

- `captcha_image_src();`
- `Captcha::src();`

## Get Html Tag `<img>`

- `captcha_image_html();`
- `Captcha::html();`

## License

Licensed under the [MIT](LICENSE.md) license

## Credits

- Thanks to Cory LaViska for the base code and Yasir M Türk for the: <https://github.com/yasirmturk/simple-php-captcha>
- Special thanks to Subtle Patterns for the patterns used for default backgrounds: <http://subtlepatterns.com>
- Special thanks to dafont.com for providing Times New Yorker: <http://www.dafont.com>
- Special thanks to Fonthead Design for providing GoodDog: <http://www.fonthead.com>
