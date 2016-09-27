<?php

namespace MPSToolbox\Api;

class FMAudit {

    private $base_uri;

    /** @var \GuzzleHttp\Client  */
    private $client;

    public function __construct($base_uri) {
        $this->base_uri = $base_uri;
    }

    public function login($email, $password) {
        $fp = fopen(APPLICATION_BASE_PATH . '/data/logs/curl.fmaudit.log', 'wb');
        $opt=[
            'base_uri' => $this->base_uri,
            'allow_redirects' => false,
            'verify'=>false,
            'cookies' => true,
            'debug'=>true,
            'headers' => [
                'User-Agent'=>'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:48.0) Gecko/20100101 Firefox/48.0',
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'Accept-Encoding'=>'gzip, deflate',
                'Accept-Language'=>'en-US,en;q=0.5',
                'Connection'=>'keep-alive',
            ],
            'curl' => [
                //CURLOPT_VERBOSE=> 1,
                CURLOPT_STDERR => $fp
            ]
        ];
        $this->client = new \GuzzleHttp\Client($opt);

        /**/
        $response = $this->client->get('Login');
        sleep(1);

        if ($response->getStatusCode()!='200') {
            error_log('Response code != 200');
            return false;
        }

        $content = $response->getBody()->getContents();
        if (!preg_match('#"__RequestVerificationToken" type="hidden" value="([^"]+)"#',$content,$match)) {
            error_log('__RequestVerificationToken not found');
        }
        $requestVerificationToken = $match[1];

        if (!preg_match('#var publicKeyExponent \= Base64\.decode\("([^"]+)"\)#',$content,$match)) {
            error_log('publicKeyExponent not found');
        }
        $publicKeyExponent = new \phpseclib\Math\BigInteger(base64_decode($match[1]), 256);

        if (!preg_match('#var publicKeyModulus \= Base64\.decode\("([^"]+)"\)#',$content,$match)) {
            error_log('publicKeyModulus not found');
        }
        $publicKeyModulus = new \phpseclib\Math\BigInteger(base64_decode($match[1]), 256);

        $rsa = new \phpseclib\Crypt\RSA();
        $rsa->loadKey(['e'=>$publicKeyExponent, 'n'=>$publicKeyModulus]);
        $rsa->setPublicKey();
        $rsa->setEncryptionMode(\phpseclib\Crypt\RSA::ENCRYPTION_PKCS1);
        $encryptedPassword = base64_encode($rsa->encrypt($password));

        $response = $this->client->post('Login', [
            'form_params' => [
                '__RequestVerificationToken'=>$requestVerificationToken,
                'email'=>$email,
                'passhash'=>$encryptedPassword,
                'password'=>'',
            ]
        ]);
        sleep(1);
        if ($response->getStatusCode()!='302') {
            error_log('Unexpected status code: '.$response->getStatusCode());
            return false;
        }
        if ($response->getHeaderLine('Location')!='/') {
            error_log('Unexpected Location: '.$response->getHeaderLine('Location'));
            return false;
        }
        /**/
        return true;
    }

    /**
     * @param $url
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function get($url, $options=array()) {
        $result = $this->client->get($url, $options);
        sleep(1);
        return $result;
    }

    public function post($url, $form_params=array(), $options=array()) {
        $options['form_params'] = $form_params;
        $result = $this->client->post($url, $options);
        sleep(1);
        return $result;
    }

}