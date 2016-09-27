<?php

namespace MPSToolbox\Api;

class LFM {

    public $host = null;
    public $format = 'json';
    public $debug = false;

    public $user = '';
    public $pass = '';

    /**
     * @var \GuzzleHttp\Client
     */
    private $session = null;

    public function __construct($attributes) {
        foreach(['host','user','pass','debug'] as $k)
        if (isset($attributes[$k])) {
            $this->$k = $attributes[$k];
        }
    }

    /**
     * @return \GuzzleHttp\Client
     */
    private function getSession() {
        if (empty($this->session)) {
            $opt=[
                'allow_redirects' => false,
                'verify'=>false,
                'base_uri' => 'http://'.$this->host.'/com.lexmark.lfm.api/'.$this->format.'/',
                'cookies' => true,
                'debug' => $this->debug,
            ];
            $this->session = new \GuzzleHttp\Client($opt);
        }
        return $this->session;
    }

    private function request($method, $query=null) {
        $session = $this->getSession();

        $response = $session->get($method, $query ? ['query' => $query] : []);

        if (($response->getStatusCode()==302) || ($response->getStatusCode() == 301)) {
            if (preg_match('#/login/auth#',$response->getHeaderLine('Location'))) {
                /**/
                $auth_response = $this->session->get($response->getHeaderLine('Location'));
                if ($auth_response->getStatusCode() != 200) {
                    throw new \Exception('Cannot connect to LFM auth page, status code='.$response->getStatusCode());
                }

                $auth_response = $this->session->post('/j_spring_security_check', [
                    'form_params' => [
                        'j_username'=>$this->user,
                        'j_password'=>$this->pass,
                    ]
                ]);
                if (($auth_response->getStatusCode()==302) || ($auth_response->getStatusCode()==301)) {
                    if (preg_match('#'.$method.'#',$auth_response->getHeaderLine('Location'))) {

                        $response = $session->get($method, $query ? ['query' => $query] : []);

                    }
                } else {
                    throw new \Exception('Login failed, status code='.$response->getStatusCode());
                }
            }
        }
        if ($response->getStatusCode() != 200) {
            throw new \Exception('Request failed, status code='.$response->getStatusCode());
        }

        $body = $response->getBody()->getContents();

        switch($this->format) {
            case 'json': {
                $result = json_decode($body, true);
                return $result;
            }
        }

        return null;
    }

    public function licensed() {
        return $this->request('licensed');
    }

    public function version() {
        return $this->request('version');
    }

    public function client() {
        return $this->request('client');
    }

    public function getPrintersForClient($clientid, $start=null, $limit=null) {
        $query = ['clientid'=>$clientid];
        if ($start) $query['start'] = $start;
        if ($limit) $query['limit'] = $limit;
        $result = $this->request('getprintersforclient', $query);

        if (isset($result['PrinterDetails']['PrinterId'])) {
            $result['PrinterDetails'] = [$result['PrinterDetails']];
        }

        return $result;
    }

    public function getPrinterCountHistoryForClient($clientid, $starttime=null, $endtime=null, $start=null, $limit=null) {
        $query = ['clientid'=>$clientid];
        if ($start) $query['start'] = $start;
        if ($limit) $query['limit'] = $limit;
        if ($limit) $query['starttime'] = $starttime;
        if ($limit) $query['endtime'] = $endtime;
        $result = $this->request('getprintercounthistoryforclient', $query);

        if (isset($result['PrinterCountRecordDetails']['PrinterId'])) {
            $result['PrinterCountRecordDetails'] = [$result['PrinterCountRecordDetails']];
        }

        return $result;
    }

    public function getPrinterStatusHistoryForClient($clientid, $starttime=null, $endtime=null, $start=null, $limit=null) {
        $query = ['clientid'=>$clientid];
        if ($start) $query['start'] = $start;
        if ($limit) $query['limit'] = $limit;
        if ($limit) $query['starttime'] = $starttime;
        if ($limit) $query['endtime'] = $endtime;
        $result = $this->request('getprinterstatushistoryforclient', $query);

        if (isset($result['PrinterStatusDetails']['PrinterId'])) {
            $result['PrinterStatusDetails'] = [$result['PrinterStatusDetails']];
        }

        return $result;
    }

    public function getPrinterSupplyHistoryForClient($clientid, $starttime=null, $endtime=null, $start=null, $limit=null) {
        $query = ['clientid'=>$clientid];
        if ($start) $query['start'] = $start;
        if ($limit) $query['limit'] = $limit;
        if ($limit) $query['starttime'] = $starttime;
        if ($limit) $query['endtime'] = $endtime;
        $result = $this->request('getprintersupplyhistoryforclient', $query);

        if (isset($result['PrinterSupplyDetails']['PrinterId'])) {
            $result['PrinterSupplyDetails'] = [$result['PrinterSupplyDetails']];
        }

        return $result;
    }


}