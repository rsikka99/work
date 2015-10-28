<?php

namespace MPSToolbox\Services;
use cdyweb\http\guzzle\Guzzle;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;

/**
 * Class HardwareService
 * @package MPSToolbox\Services
 */
class RmsUpdateService {
    
    /** @var \cdyweb\http\BaseAdapter */
    private $http;

    /**
     * @return \cdyweb\http\BaseAdapter
     */
    public function getHttp()
    {
        if (empty($this->http)) {
            $this->http = Guzzle::getAdapter();
        }
        return $this->http;
    }

    /**
     * @param \cdyweb\http\BaseAdapter $http
     */
    public function setHttp($http)
    {
        $this->http = $http;
    }



    public function update($rmsUri, $groupId) {
        $uri = parse_url($rmsUri);

        $http = $this->getHttp();
        $http->appendRequestHeader('Accept','application/json');
        $http->setBasicAuth($uri['user'],$uri['pass']);
        $base_path = 'https://'.$uri['host'].'/restapi/3.5.5';

        //throw Exception when auth fails
        $http->put($base_path.'/auth');

        $response = $http->get($base_path.'/devices?'.http_build_query(['groupId'=>$groupId]));
        echo $response->getBody()->getContents();
    }

    public function getRmsClients() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        return $db->fetchAll('
SELECT c.id as clientId, c.dealerId, c.deviceGroup, ss.rmsUri
FROM `clients` c
JOIN `dealer_settings` ds ON c.dealerId = ds.dealerId
JOIN `shop_settings` ss ON ds.shopSettingsId = ss.id
WHERE c.deviceGroup IS NOT NULL
group by clientId
        ');
    }


    /*
    public function processMessage(Message $message) {
        if (!preg_match('#(\d+)#', $message->getSubject(), $match)) {
            throw new \RuntimeException('No client ID in subject');
        }
        $client_id = $match[1];
        $client = ClientMapper::getInstance()->find($client_id);
        if (!$client) {
            throw new \RuntimeException('Client not found: '.$client_id);
        }

        $arr = $message->getAttachments();
        if (count($arr)!==1) {
            throw new \RuntimeException('Not exactly one attachment found');
        }
        ** @var \Fetch\Attachment $attachment *
        $attachment = current($arr);
        if (!preg_match('#\.csv$#i', $attachment->getFileName())) {
            throw new \RuntimeException('Attachment is not CSV');
        }
        $attachment->getData();
    }
    /**/

}