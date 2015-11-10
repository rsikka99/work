<?php

namespace MPSToolbox\Services;
use cdyweb\http\Exception\RequestException;
use cdyweb\http\guzzle\Guzzle;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\HTML;
use MPSToolbox\Entities\ClientEntity;
use MPSToolbox\Entities\DeviceNeedsTonerEntity;
use MPSToolbox\Entities\RmsUpdateEntity;
use MPSToolbox\Entities\TonerColorEntity;
use MPSToolbox\Entities\TonerEntity;
use MPSToolbox\Legacy\Mappers\DealerBrandingMapper;
use MPSToolbox\Legacy\Models\DealerBrandingModel;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\TonerColorModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ClientMapper;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\ContactMapper;
use MPSToolbox\Settings\Entities\DealerSettingsEntity;
use MPSToolbox\Settings\Entities\ShopSettingsEntity;

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

    public function findDeviceInstance($clientId, $line) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare(
"
select dimd.masterDeviceId, di.*, r.*
from device_instances di
  join device_instance_master_devices dimd on di.id=dimd.deviceInstanceId
  join rms_upload_rows r on di.rmsUploadRowId=r.id
where rmsUploadId in (select id from rms_uploads where clientId=:clientId)
    and ipAddress=:ipAddress
    and serialNumber=:serialNumber
order by rmsUploadRowId DESC
limit 1
"
        );
        $st->execute([':clientId'=>$clientId,':ipAddress'=>$line['ipAddress'],':serialNumber'=>$line['serialNumber']]);
        return $st->fetch(\PDO::FETCH_ASSOC);
    }

    private function readUrl($url) {
        /** @var \Zend_Log $log */
        $log = \Zend_Registry::get('Zend_Log');
        $log->log($url, 4);
        if (file_exists('c:')) {
            $parsed = parse_url($url);
            $filename = 'data/cache/'.basename($parsed['path']) . '.' . md5($url) . '.txt';
            if (file_exists($filename)) return new Response(200, [], file_get_contents($filename));
        }
        $result = $this->getHttp()->get($url);
        if (file_exists('c:')) {
            $body = $result->getBody()->getContents();
            file_put_contents($filename, json_encode(json_decode($body, true), JSON_PRETTY_PRINT));
            $result = new Response(200, [], file_get_contents($filename));
        }
        return $result;
    }

    public function update($clientId, $rmsUri, $groupId) {
        if (empty($groupId)) {
            throw new \InvalidArgumentException('$groupId is empty');
        }
        $result = [];
        $uri = parse_url($rmsUri);

        $http = $this->getHttp();
        $http->appendRequestHeader('Accept','application/json');
        $http->appendRequestHeader('X-API-KEY','eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJvZmYiOmZhbHNlLCJzdWIiOiJOb3JtIE1jQ29ua2V5IChQYXJ0cyBOb3cpIiwiYXVkIjoiYzE4ZTQ4MWUtZGY0OS00NDdjLWI2MmMtZmI5NTljNjcxYjFkIiwiaWF0IjoxNDQ2MjIzMzkxLCJuYmYiOjE0NDYxNjMyMDAsImV4cCI6bnVsbH0.oS8yjz9-P4hIIU8cx8aZQhryxiDUfRm04QL969sCPbrl2TqBQ8XTtMRm5ymiVuTIBof8EvzDfKfAi26Ca83DQg');
        $http->setBasicAuth($uri['user'],$uri['pass']);
        $base_path = 'https://'.$uri['host'].'/restapi/3.5.5';

        //throw Exception when auth fails
        try {
            $http->put($base_path . '/auth');
        } catch(RequestException $ex) {
            $r = $ex->getResponse();
            throw new \RuntimeException('Auth failed: '.$r->getBody()->getContents(), -1, $ex);
        }

        $url=$base_path.'/devices?'.http_build_query(['groupId'=>$groupId,'includeSubGroups'=>'true']);
        $response = $this->readUrl($url); //$http->get($url);
        $str = $response->getBody()->getContents();
        $json = json_decode($str, true);

        /** @var ClientEntity $client */
        $client = ClientEntity::find($clientId);
        $ignoreMasterDevices = explode(',',$client->getNotSupportedMasterDevices());

        foreach ($json as $line) {
            if ($line['managementStatus']!='Managed') continue;
            if ($line['licenseStatus']!='Full') continue;

            $device_instance = $this->findDeviceInstance($clientId, $line);
            if (!$device_instance) {
                echo "!!! {$line['id']} {$line['name']} {$line['ipAddress']} {$line['serialNumber']} <br>\n";
                continue;
            }
            if (in_array($device_instance['masterDeviceId'], $ignoreMasterDevices)) {
                echo "ignoring: {$line['id']} {$line['name']} {$line['ipAddress']} {$line['serialNumber']} <br>\n";
                continue;
            }

            echo "{$line['id']} {$line['name']} {$line['ipAddress']} {$line['serialNumber']} <br>\n";

            $url=$base_path.'/devices/'.$line['id'];
            $response = $this->readUrl($url); //$http->get($url);
            $str = $response->getBody()->getContents();
            $device = json_decode($str, true);

            $today = date('Y-m-d', strtotime('+1 DAY'));
            $minus90 = date('Y-m-d', strtotime('-90 DAY'));
            $url=$base_path.'/devices/'.$device['id'].'/meters?'.http_build_query(['startDate'=>$minus90.'T00:00:00Z','endDate'=>$today.'T00:00:00Z','intervalUnit'=>'yearly']);
            #echo "{$url} <br>\n";
            $response = $this->readUrl($url); //$http->get($url);
            $str = $response->getBody()->getContents();
            $meters = json_decode($str, true);
            foreach ($meters as $meter) {
                if (empty($meter['delta'])) $meter['delta']=0;
                if (empty($meter['count'])) $meter['count']=0;
                $meters[$meter['label']] = $meter;
            }

            $reportsTonerLevels = isset($device['colorSupplies']) && (count($device['colorSupplies'])>0);
            $isColor = isset($meters['Total Color Units Output']);

            $data=[
                'clientId'=>$clientId,
                'assetId'=>$line['id'],
                'ipAddress'=>$line['ipAddress'],
                'serialNumber'=>$line['serialNumber'],
                'location'=>$line['location'],
                'rawDeviceName'=>$line['name'],
                'masterDeviceId'=>$device_instance['masterDeviceId'],
                'rmsProviderId'=>6, //Printfleet 3.x
                'isColor'=>$isColor?1:0,
                'isCopier'=>isset($meters['COPIERTOTAL'])?'1':'0',
                'isFax'=>isset($meters['FAXMONO'])?'1':'0',
                'isLeased'=>$device_instance['isLeased'],
                'reportsTonerLevels'=>$reportsTonerLevels ?'1':'0',
                'ppmBlack'=>$device_instance['ppmBlack'],
                'ppmColor'=>$device_instance['ppmColor'],
                'wattsPowerNormal'=>$device_instance['wattsPowerNormal'],
                'wattsPowerIdle'=>$device_instance['wattsPowerIdle'],
                'tonerLevelBlack'=>$reportsTonerLevels? $device['colorSupplies']['black']['level']['lowPercent'] : null,
                'tonerLevelCyan'=>$reportsTonerLevels && $isColor ? $device['colorSupplies']['cyan']['level']['lowPercent'] : null,
                'tonerLevelMagenta'=>$reportsTonerLevels && $isColor ? $device['colorSupplies']['magenta']['level']['lowPercent'] : null,
                'tonerLevelYellow'=>$reportsTonerLevels && $isColor ? $device['colorSupplies']['yellow']['level']['lowPercent'] : null,
                'pageCoverageMonochrome'=>$device_instance['pageCoverageMonochrome'],
                'pageCoverageCyan'=>$device_instance['pageCoverageCyan'],
                'pageCoverageMagenta'=>$device_instance['pageCoverageMagenta'],
                'pageCoverageYellow'=>$device_instance['pageCoverageYellow'],
                'pageCoverageColor'=>$device_instance['pageCoverageColor'],
                'monitorStartDate'=>$minus90,
                'monitorEndDate'=>$today,
                'startMeterBlack'=>$meters['Total Mono Units Output']['count'] - $meters['Total Mono Units Output']['delta'],
                'endMeterBlack'=>$meters['Total Mono Units Output']['count'],
                'startMeterColor'=>$isColor ? $meters['Total Color Units Output']['count'] - $meters['Total Color Units Output']['delta'] : null,
                'endMeterColor'=>$isColor ? $meters['Total Color Units Output']['count'] : null,
                'startMeterLife'=>$meters['Total Units Output']['count'] - $meters['Total Units Output']['delta'],
                'endMeterLife'=>$meters['Total Units Output']['count'],
            ];

            $this->replaceRmsUpdate($data);
            $result[] = RmsUpdateEntity::find([
                'client'=>$clientId,
                'assetId'=>$line['id'],
                'ipAddress'=>$line['ipAddress'],
                'serialNumber'=>$line['serialNumber']
            ]);
        }
        return $result;
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


    public function replaceRmsUpdate($data) {
        $fields = ['clientId','assetId','ipAddress','serialNumber','location','rawDeviceName','masterDeviceId','rmsProviderId','isColor','isCopier','isFax','isLeased','isDuplex','isA3','reportsTonerLevels','launchDate','ppmBlack','ppmColor','wattsPowerNormal','wattsPowerIdle','tonerLevelBlack','tonerLevelCyan','tonerLevelMagenta','tonerLevelYellow','pageCoverageMonochrome','pageCoverageCyan','pageCoverageMagenta','pageCoverageYellow','pageCoverageColor','mpsDiscoveryDate','isManaged','monitorStartDate','monitorEndDate','startMeterBlack','endMeterBlack','startMeterColor','endMeterColor','startMeterPrintBlack','endMeterPrintBlack','startMeterPrintColor','endMeterPrintColor','startMeterCopyBlack','endMeterCopyBlack','startMeterCopyColor','endMeterCopyColor','startMeterFax','endMeterFax','startMeterScan','endMeterScan','startMeterPrintA3Black','endMeterPrintA3Black','startMeterPrintA3Color','endMeterPrintA3Color','startMeterLife','endMeterLife'];

        $str1='`'.implode('`,`',$fields).'`';
        $str2=':'.implode(',:',$fields);

        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare("replace into rms_update ( {$str1} ) values ( {$str2} )");
        foreach ($fields as $field) if (!isset($data[$field])) $data[$field]=null;

        $st->execute($data);
    }

    public function tonerMayBeReplaced(RmsUpdateEntity $device, $colorId) {
        /** @var DeviceNeedsTonerEntity $deviceNeedsToner */
        $deviceNeedsToner = DeviceNeedsTonerEntity::find([
            'color'=>$colorId,
            'client'=>$device->getClient(),
            'assetId'=>$device->getAssetId(),
            'ipAddress'=>$device->getIpAddress(),
            'serialNumber'=>$device->getSerialNumber()
        ]);
        if (empty($deviceNeedsToner)) {
            return;
        }
        switch ($colorId) {
            case TonerColorEntity::BLACK : { if ($device->getTonerLevelBlack()<=$deviceNeedsToner->getTonerLevel()) return; break; }
            case TonerColorEntity::MAGENTA : { if ($device->getTonerLevelMagenta()<=$deviceNeedsToner->getTonerLevel()) return; break; }
            case TonerColorEntity::CYAN : { if ($device->getTonerLevelCyan()<=$deviceNeedsToner->getTonerLevel()) return; break; }
            case TonerColorEntity::YELLOW : { if ($device->getTonerLevelYellow()<=$deviceNeedsToner->getTonerLevel()) return; break; }
        }
        printf("Toner has been replaced for device: %s <br>\n", $device->getIpAddress());
        $deviceNeedsToner->delete();
    }

    public function deviceNeedsToner(RmsUpdateEntity $device, $client, $colorId) {
        $result = false;
        $masterDevice = $device->getMasterDevice();
        if (!$masterDevice) {
            error_log(sprintf('%s:%s masterDevice unknown', __FILE__, __LINE__));
            return;
        }
        $mfg = $masterDevice->getManufacturer();
        if (!$mfg) {
            error_log(sprintf('%s:%s manufacturer unknown', __FILE__, __LINE__));
            return;
        }

        $toners = [];
        foreach ($masterDevice->getToners() as $toner) {
            /** @var TonerEntity $toner */
            if ($toner->getTonerColor()->getId() == $colorId) {
                $toners[] = $toner;
            }
        }

        $clientSettingsService = new \MPSToolbox\Settings\Service\ClientSettingsService();
        $settings = $clientSettingsService->getClientSettings($client['clientId'], $client['dealerId']);
        $rank = $settings->currentFleetSettings->getMonochromeRankSet();
        if ($colorId!=TonerColorEntity::BLACK) {
            $rank = $settings->currentFleetSettings->getColorRankSet();
        }
        $arr = $rank->getRanksAsArray();
        $arr[] = $mfg->getId();
        $tonerOptions=[];
        $tonerOptionIds=[];
        foreach ($arr as $i=>$manufacturerId) {
            foreach ($toners as $j=>$toner) {
                /** @var TonerEntity $toner */
                if ($toner->getManufacturer()->getId() == $manufacturerId) {
                    $tonerOptions[] = $toner;
                    $tonerOptionIds[] = $toner->getId();
                    unset($toners[$j]);
                }
            }
        }
        foreach ($toners as $toner) {
            $tonerOptions[] = $toner;
            $tonerOptionIds[] = $toner->getId();
        }

        if (empty($tonerOptions)) {
            error_log(sprintf('%s:%s no toner options', __FILE__, __LINE__));
            return;
        }

        $toner = current($tonerOptions);

        /** @var \MPSToolbox\Entities\DeviceNeedsTonerEntity $deviceNeedsToner */
        $deviceNeedsToner = \MPSToolbox\Entities\DeviceNeedsTonerEntity::find([
            'color'=>$colorId,
            'client'=>$device->getClient(),
            'assetId'=>$device->getAssetId(),
            'ipAddress'=>$device->getIpAddress(),
            'serialNumber'=>$device->getSerialNumber()
        ]);
        if (empty($deviceNeedsToner)) {
            printf("NEW Device Needs Toner! %s <br>\n", $device->getIpAddress());
            $deviceNeedsToner = new \MPSToolbox\Entities\DeviceNeedsTonerEntity();
            $deviceNeedsToner->setColor(TonerColorEntity::find($colorId));
            $deviceNeedsToner->setClient($device->getClient());
            $deviceNeedsToner->setAssetId($device->getAssetId());
            $deviceNeedsToner->setIpAddress($device->getIpAddress());
            $deviceNeedsToner->setSerialNumber($device->getSerialNumber());
            $deviceNeedsToner->setLocation($device->getLocation());
            $deviceNeedsToner->setFirstReported(new \DateTime());
            $deviceNeedsToner->setMasterDevice($masterDevice);
            $deviceNeedsToner->setTonerOptions(implode(',', $tonerOptionIds));
            $deviceNeedsToner->setToner($toner);
            $result = true;
        } else {
            printf("Device Needs Toner update %s <br>\n", $device->getIpAddress());
        }

        $deviceNeedsToner->setDaysLeft($device->getDaysLeft($colorId));

        switch ($colorId) {
            case TonerColorEntity::BLACK : { $deviceNeedsToner->setTonerLevel($device->getTonerLevelBlack()); break; }
            case TonerColorEntity::MAGENTA : { $deviceNeedsToner->setTonerLevel($device->getTonerLevelMagenta()); break; }
            case TonerColorEntity::CYAN : { $deviceNeedsToner->setTonerLevel($device->getTonerLevelCyan()); break; }
            case TonerColorEntity::YELLOW : { $deviceNeedsToner->setTonerLevel($device->getTonerLevelYellow()); break; }
        }

        $deviceNeedsToner->save();
        return $result;
    }

    public function sendEmail($c) {
        /** @var ClientEntity $client */
        $client = ClientEntity::find($c['clientId']);
        if (empty($client)) {
            error_log('Client not found: '.$c['clientId']);
            return;
        }
        $dealerId = $client->getDealer()->getId();

        $contact = ContactMapper::getInstance()->getContactByClientId($client->getId());
        if (empty($contact)) {
            error_log('No contact person found for client: '.$c['clientId']);
            return;
        }
        if (empty($contact->email)) {
            error_log('No email defined for contact, client: '.$c['clientId']);
        }
        $contactName = $contact->firstName.' '.$contact->lastName;

        $dealerBranding = DealerBrandingMapper::getInstance()->find($dealerId);
        if (empty($dealerBranding)) {
            error_log('DealerBranding not found, dealer: '.$dealerId);
            return;
        }
        if (empty($dealerBranding->dealerEmail)) {
            error_log('DealerBranding email is empty, dealer: '.$dealerId);
            return;
        }

        /** @var ShopSettingsEntity $shopSettings */
        $shopSettings = DealerSettingsEntity::getDealerSettings($dealerId)->shopSettings;
        $link = sprintf('http://%s.myshopify.com/account/login', $shopSettings->shopifyName);

        $rows = DeviceNeedsTonerEntity::getByClient($client);
        $devices = '';
        foreach ($rows as $device) {
            /** @var DeviceNeedsTonerEntity $device */
            $model = sprintf('%s %s',
                $device->getMasterDevice()->getManufacturer()->getDisplayname(),
                $device->getMasterDevice()->getModelName()
            );
            $location = '';
            if ($device->getLocation()) $location="Location: <b>{$device->getLocation()}</b><br>\n";

            $color = $device->getToner()->getTonerColor()->getName();

            $ampv = 0;
            /** @var RmsUpdateEntity $rmsUpdate */
            $rmsUpdate = RmsUpdateEntity::find([
                'client'=>$client,
                'assetId'=>$device->getAssetId(),
                'ipAddress'=>$device->getIpAddress(),
                'serialNumber'=>$device->getSerialNumber()
            ]);
            if ($rmsUpdate) {
                if ($device->getColor()->getName() == 'BLACK') {
                    $meter = $rmsUpdate->getEndMeterBlack() - $rmsUpdate->getStartMeterBlack();
                } else {
                    $meter = $rmsUpdate->getEndMeterColor() - $rmsUpdate->getStartMeterColor();
                }
                $diff = date_diff($rmsUpdate->getMonitorStartDate(), $rmsUpdate->getMonitorEndDate());
                $daily = $meter / $diff->days;
                $ampv = round($daily * 365 / 12);
            }

            $devices .=
<<<HTML
<p>
    Device Model: <b>{$model}</b><br>
    Serial Number: <b>{$device->getSerialNumber()}</b><br>
    IP Address: <b>{$device->getIpAddress()}</b><br>
    {$location}
    Toner Level {$color}: <b>{$device->getTonerLevel()}%</b><br>
    Estimated Monthly Page Volume: <b>{$ampv}</b><br>
    Estimated time left until toner reaches 5%: <b>{$device->getDaysLeft()}</b> days
</p>
HTML;
        }

        $html =
<<<HTML
<p>Hello {$contactName}</p>

<p>We have determined that the following devices require supplies:</p>

{$devices}

<p>
    <em>Note:</em> print quality typically degrades when supply levels are between 5 and 10%.
    Lighter prints or inaccurate color representation are often common when supply levels are low.
    Some devices will stop printing at any point below 5%.
    Delivery times should also be considered to ensure the device does not stop printing due to empty supplies.
</p>
<p>
    We have prepared a supply order for you. Please click here to login to your account and order:<br>
    <a href="{$link}">{$link}</a>
</p>

<p>Regards,<br>
The toner management team at {$dealerBranding->dealerName}
</p>
HTML;
;

        $email = new \Zend_Mail();
        $email->setFrom($dealerBranding->dealerEmail, $dealerBranding->dealerName);
        $email->addTo($contact->email);
        $email->addBcc($dealerBranding->dealerEmail);
        $email->setSubject('Printing Supplies Order Requirements for '.$client->getCompanyName());
        $email->setBodyHtml($html);
        $email->setBodyText(strip_tags($html));
        $email->send();
    }

}