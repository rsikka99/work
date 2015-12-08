<?php

namespace MPSToolbox\Services;
use cdyweb\http\Exception\RequestException;
use cdyweb\http\guzzle\Guzzle;
use GuzzleHttp\Psr7\Response;
use MPSToolbox\Entities\ClientEntity;
use MPSToolbox\Entities\DeviceNeedsTonerEntity;
use MPSToolbox\Entities\MasterDeviceEntity;
use MPSToolbox\Entities\RmsDeviceInstanceEntity;
use MPSToolbox\Entities\RmsUpdateEntity;
use MPSToolbox\Entities\TonerColorEntity;
use MPSToolbox\Entities\TonerEntity;
use MPSToolbox\Legacy\Mappers\DealerBrandingMapper;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Services\TonerService;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
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

    /**
    public function findDeviceInstance($clientId, $line) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('select * from rms_device_instances where clientId=:clientId and assetId=:assetId and ipAddress=:ipAddress and serialNumber=:serialNumber');
        $st->execute([':clientId'=>$clientId,':ipAddress'=>$line['ipAddress'],':serialNumber'=>$line['serialNumber'],':assetId'=>$line['id']]);
        $result = $st->fetch();
        if (!$result) {
            $st = $db->prepare('select * from rms_device_instances where clientId=:clientId and ipAddress=:ipAddress and serialNumber=:serialNumber and assetId=\'\'');
            $st->execute([':clientId'=>$clientId,':ipAddress'=>$line['ipAddress'],':serialNumber'=>$line['serialNumber']]);
            $result = $st->fetch();
        }
        return $result;
    }
    **/

    private function readUrl($url) {
        /** @var \Zend_Log $log */
        $log = \Zend_Registry::get('Zend_Log');
        $log->log($url, 4);
        $filename='';
        if (file_exists('c:') && !defined('UNIT_TESTING')) {
            $parsed = parse_url($url);
            $filename = 'data/cache/'.basename($parsed['path']) . '.' . md5($url) . '.txt';
            if (file_exists($filename)) return new Response(200, [], file_get_contents($filename));
        }
        $result = $this->getHttp()->get($url);
        if (file_exists('c:') && !defined('UNIT_TESTING')) {
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

            $device_instance = RmsDeviceInstanceEntity::findOne($clientId, $line['ipAddress'], $line['serialNumber'], $line['id']);//$this->findDeviceInstance($clientId, $line);
            if (!$device_instance) {
                echo "!!! {$line['id']} {$line['name']} {$line['ipAddress']} {$line['serialNumber']} <br>\n";
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

            $masterDevice = null;
            $masterDeviceId = null;
            $fullDeviceName = $line['modelMatch']['model']['name'];
            if ($device_instance && $device_instance->getMasterDevice()) {
                $masterDevice = MasterDeviceMapper::getInstance()->find($device_instance->getMasterDevice()->getId());
            }

            if (!$masterDevice) {
                $manufacturer = $line['modelMatch']['model']['manufacturer'];
                $modelName = str_replace("{$manufacturer} ", '', $line['modelMatch']['model']['name']);
                $masterDevice = $this->tryToMap($manufacturer,$modelName);
            }

            if ($masterDevice) {
                $fullDeviceName = $masterDevice->getFullDeviceName();
                $masterDeviceId = $masterDevice->id;
            }

            $data=[
                'rmsDeviceInstanceId' => $device_instance ? $device_instance->getId() : null,
                'clientId'=>$clientId,
                'assetId'=>$line['id'],
                'ipAddress'=>$line['ipAddress'],
                'serialNumber'=>$line['serialNumber'],
                'location'=>$line['location'],
                'rawDeviceName'=>$line['name'],
                'fullDeviceName'=>$fullDeviceName,
                'manufacturer'=>$line['modelMatch']['model']['manufacturer'],
                'modelName'=>str_replace("{$line['modelMatch']['model']['manufacturer']} ", '', $line['modelMatch']['model']['name']),
                'masterDeviceId'=>$masterDeviceId,
                'rmsProviderId'=>6, //Printfleet 3.x
                'isColor'=>$isColor?1:0,
                'isCopier'=>isset($meters['COPIERTOTAL'])?'1':'0',
                'isFax'=>isset($meters['FAXMONO'])?'1':'0',
                //'isLeased'=>$device_instance ? $device_instance['isLeased'] : null,
                'reportsTonerLevels'=>$reportsTonerLevels ?'1':'0',
                'ppmBlack'=>$masterDevice ? $masterDevice->ppmBlack : null,
                'ppmColor'=>$masterDevice ? $masterDevice->ppmColor : null,
                'wattsPowerNormal'=>$masterDevice ? $masterDevice->wattsPowerNormal : null,
                'wattsPowerIdle'=>$masterDevice ? $masterDevice->wattsPowerIdle : null,
                'tonerLevelBlack'=>$reportsTonerLevels ? $device['colorSupplies']['black']['level']['lowPercent'] : null,
                'tonerLevelCyan'=>$reportsTonerLevels && $isColor ? $device['colorSupplies']['cyan']['level']['lowPercent'] : null,
                'tonerLevelMagenta'=>$reportsTonerLevels && $isColor ? $device['colorSupplies']['magenta']['level']['lowPercent'] : null,
                'tonerLevelYellow'=>$reportsTonerLevels && $isColor ? $device['colorSupplies']['yellow']['level']['lowPercent'] : null,
                'pageCoverageMonochrome'=>isset($line['coverages']['mono']['value']) ? intval($line['coverages']['mono']['value']) : null,
                'pageCoverageCyan'=>isset($line['coverages']['color']['value']) ? intval($line['coverages']['color']['value'])/4 : null,
                'pageCoverageMagenta'=>isset($line['coverages']['color']['value']) ? intval($line['coverages']['color']['value'])/4 : null,
                'pageCoverageYellow'=>isset($line['coverages']['color']['value']) ? intval($line['coverages']['color']['value'])/4 : null,
                'pageCoverageColor'=>isset($line['coverages']['color']['value']) ? intval($line['coverages']['color']['value']) : null,
                'monitorStartDate'=>date('Y-m-d H:i:s',strtotime($meters['Total Units Output']['firstReportedAt'])),//$minus90,
                'monitorEndDate'=>date('Y-m-d H:i:s',strtotime($meters['Total Units Output']['lastReportedAt'])), //$today,
                'startMeterBlack'=>$meters['Total Mono Units Output']['count'] - $meters['Total Mono Units Output']['delta'],
                'endMeterBlack'=>$meters['Total Mono Units Output']['count'],
                'startMeterColor'=>$isColor ? $meters['Total Color Units Output']['count'] - $meters['Total Color Units Output']['delta'] : null,
                'endMeterColor'=>$isColor ? $meters['Total Color Units Output']['count'] : null,
                'startMeterPrintBlack'=>isset($meters['PRINTTOTAL']) ? $meters['PRINTTOTAL']['count'] - $meters['PRINTTOTAL']['delta'] : null,
                'endMeterPrintBlack'=>isset($meters['PRINTTOTAL']) ? $meters['PRINTTOTAL']['count'] : null,
                'startMeterPrintColor'=>null,
                'endMeterPrintColor'=>null,
                'startMeterCopyBlack'=>isset($meters['COPIERTOTAL']) ? $meters['COPIERTOTAL']['count'] - $meters['COPIERTOTAL']['delta'] : null,
                'endMeterCopyBlack'=>isset($meters['COPIERTOTAL']) ? $meters['COPIERTOTAL']['count'] : null,
                'startMeterCopyColor'=>null,
                'endMeterCopyColor'=>null,
                'startMeterFax'=>isset($meters['FAXMONO']) ? $meters['FAXMONO']['count'] - $meters['FAXMONO']['delta'] : null,
                'endMeterFax'=>isset($meters['FAXMONO']) ? $meters['FAXMONO']['count'] : null,
                'startMeterScan'=>isset($meters['SCAN']) ? $meters['SCAN']['count'] - $meters['SCAN']['delta'] : null,
                'endMeterScan'=>isset($meters['SCAN']) ? $meters['SCAN']['count'] : null,
                'startMeterLife'=>$meters['Total Units Output']['count'] - $meters['Total Units Output']['delta'],
                'endMeterLife'=>$meters['Total Units Output']['count'],
            ];

            $data['rmsDeviceInstanceId'] = $this->toDeviceInstance($data);
            $this->toRealtime($data);

            $this->replaceRmsUpdate($data);
            $result[] = RmsUpdateEntity::find($data['rmsDeviceInstanceId']);
        }
        return $result;
    }

    public function tryToMap($manufacturer, $modelName) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        #--
        $mfg = ManufacturerMapper::getInstance()->fetchByName($manufacturer);
        if ($mfg) {
            $masterDevice = MasterDeviceMapper::getInstance()->fetchByNameAndManufacturer($modelName, $mfg->id);
            if ($masterDevice) {
                return $masterDevice;
            }
        }
        #--
        $st = $db->prepare('select distinct masterDeviceId from rms_device_instances where masterDeviceId is not null and manufacturer=:mfg and modelName=:name');
        $st->execute(['mfg'=>$manufacturer, 'name'=>$modelName]);
        $arr = $st->fetchAll();
        if (empty($arr) || (count($arr)!=1)) return null;
        return MasterDeviceMapper::getInstance()->find($arr[0]['masterDeviceId']);
    }

    public function getRmsClients() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        return $db->fetchAll('
SELECT c.id as clientId, c.dealerId, c.deviceGroup, c.ecomMonochromeRank, c.ecomColorRank, c.templateNum, ss.rmsUri
FROM `clients` c
JOIN `dealer_settings` ds ON c.dealerId = ds.dealerId
JOIN `shop_settings` ss ON ds.shopSettingsId = ss.id
WHERE c.deviceGroup IS NOT NULL
group by clientId
        ');
    }


    public function replaceRmsUpdate($data) {
        //'isLeased',
        $fields = ['rmsDeviceInstanceId','rmsProviderId','isColor','isCopier','isFax','isDuplex','isA3','reportsTonerLevels','launchDate','ppmBlack','ppmColor','wattsPowerNormal','wattsPowerIdle','tonerLevelBlack','tonerLevelCyan','tonerLevelMagenta','tonerLevelYellow','pageCoverageMonochrome','pageCoverageCyan','pageCoverageMagenta','pageCoverageYellow','pageCoverageColor','mpsDiscoveryDate','isManaged','monitorStartDate','monitorEndDate','startMeterBlack','endMeterBlack','startMeterColor','endMeterColor','startMeterPrintBlack','endMeterPrintBlack','startMeterPrintColor','endMeterPrintColor','startMeterCopyBlack','endMeterCopyBlack','startMeterCopyColor','endMeterCopyColor','startMeterFax','endMeterFax','startMeterScan','endMeterScan','startMeterPrintA3Black','endMeterPrintA3Black','startMeterPrintA3Color','endMeterPrintA3Color','startMeterLife','endMeterLife'];

        $str1='`'.implode('`,`',$fields).'`';
        $str2=':'.implode(',:',$fields);

        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare("replace into rms_update ( {$str1} ) values ( {$str2} )");
        foreach ($fields as $field) if (!isset($data[$field])) $data[$field]=null;
        foreach ($data as $key=>$value) if (!in_array($key, $fields)) unset($data[$key]);

        $st->execute($data);
    }

    public function toDeviceInstance($data) {
        $fields = ['clientId','assetId','ipAddress','serialNumber','masterDeviceId','location','rawDeviceName','fullDeviceName','manufacturer','modelName','reportDate'];

        $data['reportDate'] = date('Y-m-d');

        $str1='`'.implode('`,`',$fields).'`';
        $str2=':'.implode(',:',$fields);

        $db = \Zend_Db_Table::getDefaultAdapter();
        if (!empty($data['rmsDeviceInstanceId'])) {
            #$st = $db->prepare("update rms_device_instances set location=:location, masterDeviceId=:masterDeviceId, assetId=:assetId, fullDeviceName=:fullDeviceName, reportDate=:reportDate where id=:id");
            #$st->execute(['location'=>$data['location'],'masterDeviceId'=>$data['masterDeviceId'],'assetId'=>$data['assetId'],'fullDeviceName'=>$data['fullDeviceName'],'reportDate'=>$data['reportDate'], 'id'=>$data['rmsDeviceInstanceId']]);

            $masterDevice = MasterDeviceEntity::find($data['masterDeviceId']);
            /** @var RmsDeviceInstanceEntity $obj */
            $obj = RmsDeviceInstanceEntity::find($data['rmsDeviceInstanceId']);
            $obj->setLocation($data['location']);
            $obj->setMasterDevice($masterDevice);
            $obj->setAssetId($data['assetId']);
            $obj->setFullDeviceName($data['fullDeviceName']);
            $obj->setReportDate(new \DateTime($data['reportDate']));
            $obj->save();
            return $data['rmsDeviceInstanceId'];
        }

        $st = $db->prepare("replace into rms_device_instances ( {$str1} ) values ( {$str2} )");
        foreach ($fields as $field) if (!isset($data[$field])) $data[$field] = null;
        foreach ($data as $key => $value) if (!in_array($key, $fields)) unset($data[$key]);
        $st->execute($data);
        return $db->lastInsertId();
    }

    public function toRealtime($data) {
        $fields = ['rmsDeviceInstanceId','scanDate','rmsProviderId','tonerLevelBlack','tonerLevelCyan','tonerLevelMagenta','tonerLevelYellow','lifeCountBlack','lifeCountColor','printCountBlack','printCountColor','copyCountBlack','copyCountColor','faxCount','scanCount','lifeCount'];

        $str1='`'.implode('`,`',$fields).'`';
        $str2=':'.implode(',:',$fields);

        $data['scanDate'] = date('Y-m-d');
        $data['lifeCountBlack'] = $data['endMeterBlack'];
        $data['lifeCountColor'] = $data['endMeterColor'];
        $data['printCountBlack'] = $data['endMeterPrintBlack'];
        #$data['printCountColor'] = $data['x'];
        $data['copyCountBlack'] = $data['endMeterCopyBlack'];
        #$data['copyCountColor'] = $data['x'];
        $data['faxCount'] = $data['endMeterFax'];
        $data['scanCount'] = $data['endMeterScan'];
        $data['lifeCount'] = $data['endMeterLife'];

        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare("replace into rms_realtime ( {$str1} ) values ( {$str2} )");
        foreach ($fields as $field) if (!isset($data[$field])) $data[$field]=null;
        foreach ($data as $key=>$value) if (!in_array($key, $fields)) unset($data[$key]);

        $st->execute($data);
    }

    public function tonerMayBeReplaced(RmsUpdateEntity $device, $colorId) {
        /** @var DeviceNeedsTonerEntity $deviceNeedsToner */
        $deviceNeedsToner = DeviceNeedsTonerEntity::find(['color'=>$colorId, 'rmsDeviceInstance'=>$device->getRmsDeviceInstance()]);
        if (empty($deviceNeedsToner)) {
            return;
        }
        switch ($colorId) {
            case TonerColorEntity::BLACK : { if ($device->getTonerLevelBlack()<=$deviceNeedsToner->getTonerLevel()) return; break; }
            case TonerColorEntity::MAGENTA : { if ($device->getTonerLevelMagenta()<=$deviceNeedsToner->getTonerLevel()) return; break; }
            case TonerColorEntity::CYAN : { if ($device->getTonerLevelCyan()<=$deviceNeedsToner->getTonerLevel()) return; break; }
            case TonerColorEntity::YELLOW : { if ($device->getTonerLevelYellow()<=$deviceNeedsToner->getTonerLevel()) return; break; }
        }
        printf("Toner has been replaced for device: %s <br>\n", $device->getRmsDeviceInstance()->getIpAddress());
        $deviceNeedsToner->delete();
    }

    public function deviceNeedsToner(RmsUpdateEntity $device, $client, $colorId) {
        $result = false;
        $masterDevice = $device->getRmsDeviceInstance()->getMasterDevice();
        if (!$masterDevice) {
            error_log(sprintf('%s:%s masterDevice unknown', __FILE__, __LINE__));
            return null;
        }
        $mfg = $masterDevice->getManufacturer();
        if (!$mfg) {
            error_log(sprintf('%s:%s manufacturer unknown', __FILE__, __LINE__));
            return null;
        }

        $tonerService = new TonerService();
        $toners = [];
        foreach ($masterDevice->getToners() as $toner) {
            /** @var TonerEntity $toner */
            if ($toner->getTonerColor()->getId() == $colorId) {
                $price = $tonerService->getTonerPrice($toner->getId());
                if ($price) {
                    $toners[] = $toner;
                }
            }
        }

        if ($colorId==TonerColorEntity::BLACK) {
            $arr = explode(',',$client['ecomMonochromeRank']);
        } else {
            $arr = explode(',',$client['ecomColorRank']);
        }
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
        #foreach ($toners as $toner) {
        #    $tonerOptions[] = $toner;
        #    $tonerOptionIds[] = $toner->getId();
        #}

        if (empty($tonerOptions)) {
            error_log(sprintf('%s:%s no toner options', __FILE__, __LINE__));
            return null;
        }

        $toner = current($tonerOptions);

        /** @var \MPSToolbox\Entities\DeviceNeedsTonerEntity $deviceNeedsToner */
        $deviceNeedsToner = \MPSToolbox\Entities\DeviceNeedsTonerEntity::find([
            'color'=>$colorId,
            'rmsDeviceInstance'=>$device->getRmsDeviceInstance(),
        ]);
        if (empty($deviceNeedsToner)) {
            printf("NEW Device Needs Toner! %s <br>\n", $device->getRmsDeviceInstance()->getIpAddress());
            $deviceNeedsToner = new \MPSToolbox\Entities\DeviceNeedsTonerEntity();
            $deviceNeedsToner->setRmsDeviceInstance($device->getRmsDeviceInstance());
            $deviceNeedsToner->setColor(TonerColorEntity::find($colorId));
            $deviceNeedsToner->setClient($device->getRmsDeviceInstance()->getClient());
            $deviceNeedsToner->setAssetId($device->getRmsDeviceInstance()->getAssetId());
            $deviceNeedsToner->setIpAddress($device->getRmsDeviceInstance()->getIpAddress());
            $deviceNeedsToner->setSerialNumber($device->getRmsDeviceInstance()->getSerialNumber());
            $deviceNeedsToner->setLocation($device->getRmsDeviceInstance()->getLocation());
            $deviceNeedsToner->setFirstReported(new \DateTime());
            $deviceNeedsToner->setMasterDevice($masterDevice);
            $deviceNeedsToner->setTonerOptions(implode(',', $tonerOptionIds));
            $deviceNeedsToner->setToner($toner);
            $result = true;
        } else {
            if ($deviceNeedsToner->getShopifyOrder()) {
                printf("Device Needs Toner update %s but toner has been ordered, order id: %s <br>\n", $device->getRmsDeviceInstance()->getIpAddress(), $deviceNeedsToner->getShopifyOrder());
            } else {
                $result = true;
                printf("Device Needs Toner update %s <br>\n", $device->getRmsDeviceInstance()->getIpAddress());
            }
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

        $emailFromAddress = $shopSettings->emailFromAddress;
        $emailFromName = $shopSettings->emailFromName;

        switch($c['templateNum']) {
            case 2:
                $supplyNotifySubject = $shopSettings->supplyNotifySubject2;
                $supplyNotifyMessage = $shopSettings->supplyNotifyMessage2;
                break;
            case 3:
                $supplyNotifySubject = $shopSettings->supplyNotifySubject3;
                $supplyNotifyMessage = $shopSettings->supplyNotifyMessage3;
                break;
            default:
                $supplyNotifySubject = $shopSettings->supplyNotifySubject;
                $supplyNotifyMessage = $shopSettings->supplyNotifyMessage;
                break;
        }

        if (empty($emailFromAddress)) $emailFromAddress = $dealerBranding->dealerEmail;
        if (empty($emailFromName)) $emailFromName = $dealerBranding->dealerName;
        if (empty($supplyNotifySubject)) $supplyNotifySubject = 'Printing Supplies Order Requirements for {{clientName}}';
        if (empty($supplyNotifyMessage)) $supplyNotifyMessage = <<<HTML
<p>Hello {{contactName}}</p>

<p>We have determined that the following devices require supplies:</p>

{{devices}}

<p>
    <em>Note:</em> print quality typically degrades when supply levels are between 5 and 10%.
    Lighter prints or inaccurate color representation are often common when supply levels are low.
    Some devices will stop printing at any point below 5%.
    Delivery times should also be considered to ensure the device does not stop printing due to empty supplies.
</p>
<p>
    We have prepared a supply order for you. Please click here to login to your account and order:<br>
    {{link}}
</p>

<p>Regards,<br>
The toner management team at {$dealerBranding->dealerName}
</p>
HTML;

        $rows = DeviceNeedsTonerEntity::getByClient($client);
        $arr=[];
        foreach ($rows as $device) {
            /** @var DeviceNeedsTonerEntity $device */
            $id = "{$device->getIpAddress()}-{$device->getSerialNumber()}-{$device->getAssetId()}";
            $arr[$id][] = $device;
        }

        $devices = '';
        foreach ($arr as $d) foreach ($d as $i=>$device) {
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

            $rmsUpdate = RmsUpdateEntity::find($device->getRmsDeviceInstance());

            if ($rmsUpdate) {
                if ($device->getColor()->getName() == 'BLACK') {
                    $meter = $rmsUpdate->getEndMeterBlack() - $rmsUpdate->getStartMeterBlack();
                    $meter += $rmsUpdate->getEndMeterColor() - $rmsUpdate->getStartMeterColor();
                } else {
                    $meter = $rmsUpdate->getEndMeterColor() - $rmsUpdate->getStartMeterColor();
                }
                $diff = date_diff($rmsUpdate->getMonitorStartDate(), $rmsUpdate->getMonitorEndDate());
                $daily = $meter / $diff->days;
                $ampv = round($daily * 365 / 12);
            }

            if ($i==0) $devices .=
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
            else $devices .=
                <<<HTML
                <p>
    Toner Level {$color}: <b>{$device->getTonerLevel()}%</b><br>
    Estimated Monthly Page Volume: <b>{$ampv}</b><br>
    Estimated time left until toner reaches 5%: <b>{$device->getDaysLeft()}</b> days
</p>
HTML;
        }

        $html = str_replace([
            '{{contactName}}',
            '{{devices}}',
            '{{link}}',
        ], [
            $contactName,
            $devices,
            "<a href=\"{$link}\">{$link}</a>"
        ], $supplyNotifyMessage);

        $email = new \Zend_Mail();
        $email->setFrom($emailFromAddress, $emailFromName);
        $email->addTo($contact->email);
        $email->addBcc($dealerBranding->dealerEmail);
        $email->setSubject(str_replace('{{clientName}}',$client->getCompanyName(),$supplyNotifySubject)); //'Printing Supplies Order Requirements for '.$client->getCompanyName());
        $email->setBodyHtml($html);
        $email->setBodyText(strip_tags($html));
        $email->send();
    }

    public function checkDevices($devices, $client) {
        $sendEmail = false;
        foreach ($devices as $device) {
            /** @var RmsUpdateEntity $device */

            $diff = date_diff($device->getMonitorStartDate(), $device->getMonitorEndDate());

            $color_meter = $device->getEndMeterColor() - $device->getStartMeterColor();
            $color_daily = $color_meter/$diff->days;

            $black_meter = $color_meter + ($device->getEndMeterBlack() - $device->getStartMeterBlack());
            $black_daily = $black_meter/$diff->days;

            if ($device->needsToner(TonerColorEntity::BLACK, $black_daily)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::BLACK);
            } else if ($device->getTonerLevelBlack()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::BLACK);
            }
            if ($device->needsToner(TonerColorEntity::MAGENTA, $color_daily)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::MAGENTA);
            } else if ($device->getTonerLevelMagenta()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::MAGENTA);
            }
            if ($device->needsToner(TonerColorEntity::CYAN, $color_daily)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::CYAN);
            } else if ($device->getTonerLevelCyan()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::CYAN);
            }
            if ($device->needsToner(TonerColorEntity::YELLOW, $color_daily)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::YELLOW);
            } else if ($device->getTonerLevelYellow()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::YELLOW);
            }
        }

        if ($sendEmail) {
            printf('sending email...');
            $this->sendEmail($client);
        }
    }

}