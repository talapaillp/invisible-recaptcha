<?php

namespace AlbertCht\InvisibleReCaptcha;

use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

class InvisibleReCaptcha
{
    const API_URI = 'https://www.google.com/recaptcha/api.js';
    const VERIFY_URI = 'https://www.google.com/recaptcha/api/siteverify';

    /**
     * The reCaptcha site key.
     *
     * @var string
     */
    protected $siteKey;

    /**
     * The reCaptcha secret key.
     *
     * @var string
     */
    protected $secretKey;

    /**
     * The config to determine if hide the badge.
     *
     * @var boolean
     */
    protected $hideBadge;

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Rendered number in total.
     *
     * @var integer
     */
    protected $renderedTimes = 0;

    /**
     * InvisibleReCaptcha.
     *
     * @param string $secretKey
     * @param string $siteKey
     * @param boolean $hideBadge
     */
    public function __construct($siteKey, $secretKey, $hideBadge = false)
    {
        $this->siteKey = $siteKey;
        $this->secretKey = $secretKey;
        $this->hideBadge = $hideBadge;
        $this->client = new Client(['timeout' => 5]);
    }

    /**
     * Get reCaptcha js by optional language param.
     *
     * @param string $lang
     *
     * @return string
     */
    public function getCaptchaJs($lang = null)
    {
        $api = static::API_URI . '?onload=_captchaCallback&render=explicit';
        return $lang ? $api . '&hl=' . $lang : $api;
    }

    /**
     * Render HTML reCaptcha by optional language param.
     *
     * @return string
     */
    public function render($lang = null)
    {
        $html = '';
        if ($this->renderedTimes === 0) {
            $html .= $this->initRender($lang);
        } else {
            $this->renderedTimes++;
        }
        $html .= "<div class='_g-recaptcha' id='_g-recaptcha_{$this->renderedTimes}'></div><input type='hidden' name='g-recaptcha-response'>" . PHP_EOL;

        return $html;
    }

    public function initRender($lang)
    {
        $html = '<script>var _renderedTimes,_captchaCallback,_captchaForms,_submitForm,_submitBtn;</script>';
        $html .= '<script>var _submitAction=true,_captchaForm;</script>';
        $html .= "<script>$.getScript('{$this->getCaptchaJs($lang)}').done(function(data,status,jqxhr){";
        $html .= '_renderedTimes=$("._g-recaptcha").length;_captchaForms=$("._g-recaptcha").closest("form");';
        $html .= '_captchaForms.each(function(){$(this)[0].addEventListener("submit",function(e){e.preventDefault(); $(this).form("submit"); ';
        $html .= '_captchaForm=$(this);_submitBtn=$(this).find(":submit");grecaptcha.execute();});});';
        $html .= '_submitForm=function(response){var input = _captchaForm.find("[name=\'g-recaptcha-response\']"); input.val(response);_submitBtn.trigger("captcha");grecaptcha.reset();if(_submitAction){_captchaForm.submit();}};';
        $html .= '_captchaCallback=function(){grecaptcha.render("_g-recaptcha_"+_renderedTimes,';
        $html .= "{sitekey:'{$this->siteKey}',size:'invisible',callback:_submitForm});}";
        $html .= '});</script>' . PHP_EOL;

        if ($this->hideBadge) {
            $html .= '<style>.grecaptcha-badge{display:none;!important}</style>' . PHP_EOL;
        }

        $this->renderedTimes++;
        return $html;
    }

    /**
     * Verify invisible reCaptcha response.
     *
     * @param string $response
     * @param string $clientIp
     *
     * @return bool
     */
    public function verifyResponse($response, $clientIp)
    {
        if (empty($response)) {
            return false;
        }

        $response = $this->sendVerifyRequest([
            'secret' => $this->secretKey,
            'remoteip' => $clientIp,
            'response' => $response
        ]);

        return isset($response['success']) && $response['success'] === true;
    }

    /**
     * Verify invisible reCaptcha response by Symfony Request.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function verifyRequest(Request $request)
    {
        return $this->verifyResponse(
            $request->get('g-recaptcha-response'),
            $request->getClientIp()
        );
    }

    /**
     * Send verify request.
     *
     * @param array $query
     *
     * @return array
     */
    protected function sendVerifyRequest(array $query = [])
    {
        $response = $this->client->post(static::VERIFY_URI, [
            'form_params' => $query,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Getter function of site key
     *
     * @return strnig
     */
    public function getSiteKey()
    {
        return $this->siteKey;
    }

    /**
     * Getter function of secret key
     *
     * @return strnig
     */
    public function getSecretKey()
    {
        return $this->secretKey;
    }

    /**
     * Getter function of hideBadge
     *
     * @return strnig
     */
    public function getHideBadge()
    {
        return $this->hideBadge;
    }

    /**
     * Getter function of guzzle client
     *
     * @return strnig
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Getter function of rendered times
     *
     * @return strnig
     */
    public function getRenderedTimes()
    {
        return $this->renderedTimes;
    }
}
