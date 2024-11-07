<?php

namespace Archytech\Captcha;

use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    public function getCaptcha(SimpleCaptcha $captcha)
    {
        if (ob_get_contents()) ob_clean();
        return $captcha->init();
    }

    public function getCaptchaApi(SimpleCaptcha $captcha)
    {
        return $captcha->init(true);
    }

    public function testCaptcha()
    {
        $form = '<form method="POST" action="">';
        $form .= '<input type="hidden" name="_token" value="' . csrf_token() . '">';
        $form .= '<p>' . captcha_image_html(['style' => 'border-radius: 15px; width: 192px;']) . '</p>';

        if (request()->getMethod() == 'POST') {
            $rules = ['captcha' => 'required|captcha'];
            $validator = validator()->make(request()->all(), $rules);

            if ($validator->fails()) {
                $form .= '<p><input type="text" name="captcha" style="border: 1px solid red;border-radius: 2px;" required></p>';
                $form .= '<p style="color: red;">Incorrect!</p>';
            } else {
                $form .= '<p><input type="text" name="captcha" style="border: 1px solid green;border-radius: 2px;" required></p>';
                $form .= '<p style="color: green;">Matched :)</p>';
            }
        } else {
            $form .= '<p><input type="text" name="captcha" style="border-radius: 2px;" required></p>';
        }

        $form .= '<p><button type="submit" name="check">Check</button></p>';
        $form .= '</form>';
        print $form;
        exit;
    }
}
