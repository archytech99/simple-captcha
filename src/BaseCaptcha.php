<?php

namespace Archytech\Captcha;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Hashing\BcryptHasher as Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Gd\Font;

/**
 * A simple Captcha script for Laravel Framework 6 & 7
 *
 * @copyright Copyright 2021 AriefBP.com
 * @version 1.x
 * @author Arief Budi Prasetyo
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

class BaseCaptcha implements Repository
{
    protected $charDefault = 'ICe6oXWj2ryDS6jpHHZXR9pSLXfpmLdx';
    protected $session;
    protected $imageManager;
    protected $config;
    protected $files;
    protected $hasher;
    protected $str;
    protected $characters;
    protected $canvas;
    protected $image;
    protected $backgrounds;
    protected $fontShadow;
    protected $fonts;
    private $fontSize = ['min'=> 42, 'max'=> 42];
    protected $fontColors;
    protected $lineColors;
    protected $text;
    protected $bgColor = '#ffffff';
    protected $bgStatic = true;
    protected $invert = false;
    protected $shadow = true;
    protected $bgImage = true;
    protected $encrypt = true;
    protected $shadowOffset = 2;
    protected $width = 248;
    protected $height = 66;
    protected $lines = 4;
    protected $length = 5;
    protected $blur = 0;
    protected $sharpen = 0;
    protected $expire = 60;
    protected $contrast = 0;
    protected $quality = 85;
    protected $textMarginLeft = 10;
    protected $textMarginTop = 2;

    public function __construct() {
        $this->str = new Str;
        $this->hasher = new Hash;
        $this->files = new Filesystem;
        $this->imageManager = new ImageManager;
        $this->fonts = $this->files->files(__DIR__ . '/../assets/fonts');
        $this->characters = config('captcha.characters', $this->charDefault);
        $this->backgrounds = $this->files->files(__DIR__ . '/../assets/backgrounds');
    }

    protected function config() {
        $this->bgColor = config('captcha.background.color', $this->bgColor);
        $this->bgImage = config('captcha.background.image', $this->bgImage);
        $this->bgStatic = config('captcha.background.img_static', $this->bgStatic);
        $this->width = config('captcha.background.width', $this->width);
        $this->height = config('captcha.background.height', $this->height);
        $this->contrast = config('captcha.contrast', $this->contrast);
        $this->length = config('captcha.length', $this->length);
        $this->quality = config('captcha.quality', $this->quality);
        $this->sharpen = config('captcha.sharpen', $this->sharpen);
        $this->invert = config('captcha.invert', $this->invert);
        $this->blur = config('captcha.blur', $this->blur);
        $this->shadow = config('captcha.shadow', $this->shadow);
        $this->fontColors = config('captcha.font.colors', $this->fontColors);
    }

    protected function additional()
    {
        if ($this->contrast != 0) {
            $this->image->contrast($this->contrast);
        }

        if ($this->sharpen) {
            $this->image->sharpen($this->sharpen);
        }

        if ($this->invert) {
            $this->image->invert();
        }

        if ($this->blur) {
            $this->image->blur($this->blur);
        }
    }

    protected function code(): array
    {
        $code = '';
        $characters = is_array($this->characters) ? implode($this->characters) : $this->characters;

        while( strlen($code) < $this->length ) {
            $code .= substr($characters, mt_rand() % (strlen($characters)), 1);
        }

        $hash = Crypt::encrypt($this->hasher->make($code));
        $this->session->put('captcha', [
            'key' => $hash
        ]);

        return [
            'key' => $hash,
            'code' => $code
        ];
    }

    protected function draw(): void
    {
        $marginTop = ($this->image->height() - $this->textMarginTop) / $this->length;
        $text = str_split($this->text);

        foreach ($text as $key => $char) {
            $marginLeft = $this->textMarginLeft + ($key * ($this->image->width() - mt_rand(0, $this->textMarginLeft)) / $this->length);

            if ($this->shadow) {
                $this->fontShadow = [
                    'file'=> $this->font(),
                    'size'=> $this->fontSize(),
                    'color'=> '#444'
                ];

                $this->image->text($char, $marginLeft + $this->shadowOffset, $marginTop - $this->shadowOffset,
                    function (Font $font) {
                        $font->align('left');
                        $font->valign('top');
                        $font->file($this->fontShadow['file']);
                        $font->size($this->fontShadow['size']);
                        $font->color($this->fontShadow['color']);
                    }
                );
            } else {
                $this->fontShadow = [
                    'file'=> $this->font(),
                    'size'=> $this->fontSize()
                ];
            }

            $this->image->text($char, $marginLeft, $marginTop,
                function (Font $font) {
                    $font->align('left');
                    $font->valign('top');
                    $font->file($this->fontShadow['file']);
                    $font->size($this->fontShadow['size']);
                    $font->color($this->fontColor());
                }
            );
        }
    }

    protected function background(): string
    {
        return $this->backgrounds[rand(0, count($this->backgrounds) - 1)];
    }

    protected function font(): string
    {
        return $this->fonts[rand(0, count($this->fonts) - 1)];
    }

    protected function fontSize(): int
    {
        return rand(
            $this->get('captcha.font.min_size')
            ?? $this->fontSize['min'],
            $this->get('captcha.font.max_size')
            ?? $this->fontSize['max']
        );
    }

    protected function fontColor(): string
    {
        if ($this->fontColors) {
            return $this->fontColors[rand(0, count($this->fontColors) - 1)];
        }

        return '#666';
    }

    /* Implements method in interface '\Illuminate\Contracts\Config\Repository' */
    public function has($key): void
    {
        // TODO: Implement has() method.
    }

    public function get($key, $default = null)
    {
        // TODO: Implement get() method.
    }

    public function all(): void
    {
        // TODO: Implement all() method.
    }

    public function set($key, $value = null)
    {
        // TODO: Implement set() method.
    }

    public function prepend($key, $value)
    {
        // TODO: Implement prepend() method.
    }

    public function push($key, $value)
    {
        // TODO: Implement push() method.
    }
    /* Implements method in interface 'Repository' */
}
