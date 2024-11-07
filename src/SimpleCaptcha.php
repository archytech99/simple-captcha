<?php

namespace Archytech\Captcha;

use Illuminate\Session\Store as Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\HtmlString;

class SimpleCaptcha extends BaseCaptcha
{
    public function __construct(Session $session)
    {
        parent::__construct();
        $this->session = $session;
    }

    public function init(bool $api = false)
    {
        $this->config();
        if (version_compare(app()->version(), '6.20.14', '>=')) {
            $this->fonts = array_map(function ($file) {
                return $file->getPathName();
            }, $this->fonts);
        }

        $this->fonts = array_values($this->fonts);
        if ($this->bgStatic) {
            $background = __DIR__ . '/../assets/backgrounds/45-degree-fabric.png';
        } else {
            $background = $this->background();
        }

        $captcha = $this->code();
        $this->text = $captcha['code'];
        $this->canvas = $this->imageManager->canvas(
            $this->width,
            $this->height,
            $this->bgColor
        );

        if ($this->bgImage) {
            $this->image = $this->imageManager->make($background)->resize(
                $this->width,
                $this->height
            );

            $this->canvas->insert($this->image);
        } else {
            $this->image = $this->canvas;
        }

        $this->draw();
        $this->additional();
        if ($api) {
            Cache::put('captcha_' . $captcha['key'], $captcha['code'], $this->expire);

            return [
                'key' => $captcha['key'],
                'png' => $this->image->encode('data-url')->encoded
            ];
        } else {
            return $this->image->response('png', $this->quality);
        }
    }

    public function src(): string
    {
        return url('captcha');
    }

    public function html(array $attrs = []): string
    {
        $attr = '';
        foreach ($attrs as $key => $value) {
            if ($attr == 'src') continue;
            $attr .= $key . '="' . $value . '" ';
        }
        return new HtmlString('<img src="' . $this->src() . '" ' . trim($attr) . '>');
    }

    public function check(string $value): bool
    {
        if (!$this->session->has('captcha')) {
            return false;
        }

        $key = $this->session->get('captcha.key');
        $key = Crypt::decrypt($key);
        if ($this->hasher->check($value, $key)) {
            $this->session->remove('captcha');
            return true;
        }

        return false;
    }

    public function checkApi(string $value, string $key): bool
    {
        $key = Crypt::decrypt($key);
        if (!Cache::pull('captcha_' . $key)) {
            return false;
        }

        return $this->hasher->check($value, $key);
    }
}
