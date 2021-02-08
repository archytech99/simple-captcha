<?php

if(! function_exists('hex2rgb')) {
    function hex2rgb($hex_str, $return_string = false, $separator = ',') {
        $rgb_array = [];
        $hex_str = preg_replace("/[^0-9A-Fa-f]/", '', $hex_str);
        if ( strlen($hex_str) == 6 ) {
            $color_val = hexdec($hex_str);
            $rgb_array['r'] = 0xFF & ($color_val >> 0x10);
            $rgb_array['g'] = 0xFF & ($color_val >> 0x8);
            $rgb_array['b'] = 0xFF & $color_val;
        } elseif ( strlen($hex_str) == 3 ) {
            $rgb_array['r'] = hexdec(str_repeat(substr($hex_str, 0, 1), 2));
            $rgb_array['g'] = hexdec(str_repeat(substr($hex_str, 1, 1), 2));
            $rgb_array['b'] = hexdec(str_repeat(substr($hex_str, 2, 1), 2));
        } else {
            return false;
        }
        return $return_string ? implode($separator, $rgb_array) : $rgb_array;
    }
}

if (! function_exists('captcha')) {
    function captcha()
    {
        return app('captcha')->init();
    }
}

if (! function_exists('captcha_image_src')) {
    function captcha_image_src(): string
    {
        return app('captcha')->src();
    }
}

if (! function_exists('captcha_image_html')) {
    function captcha_image_html($attrs = []): string
    {
        return app('captcha')->html($attrs);
    }
}

if (! function_exists('captcha_check')) {
    function captcha_check(string $value): bool
    {
        return app('captcha')->check($value);
    }
}

if (! function_exists('captcha_api_check')) {
    function captcha_api_check(string $value, string $key): bool
    {
        return app('captcha')->checkApi($value, $key);
    }
}
