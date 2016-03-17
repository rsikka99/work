<?php

namespace MPSToolbox\Services;
use cdyweb\http\Exception\RequestException;

use MPSToolbox\Api\PrintFleet;
use MPSToolbox\Entities\ClientEntity;
use MPSToolbox\Entities\DealerEntity;
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
use Tangent\Logger\Logger;

/**
 * Class HardwareService
 * @package MPSToolbox\Services
 */
class RmsUpdateService {

    private $printFleet;

    /**
     * @return PrintFleet
     */
    public function getPrintFleet($rmsUri)
    {
        if (empty($this->printFleet)) {
            $this->printFleet = new PrintFleet($rmsUri);
        }
        return $this->printFleet;
    }

    /**
     * @param PrintFleet $printFleet
     */
    public function setPrintFleet(PrintFleet $printFleet)
    {
        $this->printFleet = $printFleet;
    }



    /**
     * @param $clientId
     * @param $rmsUri
     * @param $groupId
     * @return RmsUpdateEntity[]
     */
    public function update($clientId, PrintFleet $printFleet, $groupId) {
        if (empty($groupId)) {
            throw new \InvalidArgumentException('$groupId is empty');
        }
        $result = [];

        if (!$printFleet->auth()) return [];

        $json = $printFleet->devices($groupId);

        foreach ($json as $line) {
            if ($line['managementStatus']!='Managed') continue;
            if ($line['licenseStatus']!='Full') continue;

            $device_instance = RmsDeviceInstanceEntity::findOne($clientId, $line['ipAddress'], $line['serialNumber'], $line['id']);//$this->findDeviceInstance($clientId, $line);
            if (!$device_instance) {
                echo "new device instance!!! {$line['id']} {$line['name']} {$line['ipAddress']} {$line['serialNumber']} <br>\n";
            } else {
                echo "updating device: {$line['id']} {$line['name']} {$line['ipAddress']} {$line['serialNumber']} <br>\n";
            }

            $device = $printFleet->device($line['id']);

            $today = date('Y-m-d', strtotime('+1 DAY'));
            $minus90 = date('Y-m-d', strtotime('-90 DAY'));
            $meters = $printFleet->meters($device['id'], $minus90, $today);
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

            $data['rmsDeviceInstanceId'] = $this->toDeviceInstance($data, $data['monitorEndDate']);
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

    public function getRmsUri($dealerId=null) {
        if (!$dealerId) $dealerId = DealerEntity::getDealerId();
        $db = \Zend_Db_Table::getDefaultAdapter();
        $line= $db->query("
SELECT ss.rmsUri
from `dealer_settings` ds
JOIN `shop_settings` ss ON ds.shopSettingsId = ss.id
WHERE ds.dealerId = {$dealerId}
")->fetch();
        return $line['rmsUri'];
    }

    public function getRmsClients() {
        $db = \Zend_Db_Table::getDefaultAdapter();
        return $db->fetchAll('
SELECT c.id as clientId, c.dealerId, c.deviceGroup, c.ecomMonochromeRank, c.ecomColorRank, c.templateNum, c.monitoringEnabled, ss.rmsUri
FROM `clients` c
JOIN `dealer_settings` ds ON c.dealerId = ds.dealerId
JOIN `shop_settings` ss ON ds.shopSettingsId = ss.id
WHERE c.deviceGroup IS NOT NULL and ss.rmsUri is not null
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

    public function toDeviceInstance($data, $reportDate=null) {
        $fields = ['clientId','assetId','ipAddress','serialNumber','masterDeviceId','location','rawDeviceName','fullDeviceName','manufacturer','modelName','reportDate'];

        if (!$reportDate) $reportDate = date('Y-m-d');
        $data['reportDate'] = $reportDate;

        $str1='`'.implode('`,`',$fields).'`';
        $str2=':'.implode(',:',$fields);

        $db = \Zend_Db_Table::getDefaultAdapter();
        if (!empty($data['rmsDeviceInstanceId'])) {
            #$st = $db->prepare("update rms_device_instances set location=:location, masterDeviceId=:masterDeviceId, assetId=:assetId, fullDeviceName=:fullDeviceName, reportDate=:reportDate where id=:id");
            #$st->execute(['location'=>$data['location'],'masterDeviceId'=>$data['masterDeviceId'],'assetId'=>$data['assetId'],'fullDeviceName'=>$data['fullDeviceName'],'reportDate'=>$data['reportDate'], 'id'=>$data['rmsDeviceInstanceId']]);

            $masterDevice = $data['masterDeviceId'] ? MasterDeviceEntity::find($data['masterDeviceId']) : null;
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
        $rmsDeviceInstance = $device->getRmsDeviceInstance();
        if ($rmsDeviceInstance->getIgnore()) {
            error_log(sprintf('ignoring %s', $device->getRmsProviderId()->getId()));
            return null;
        }
        $result = false;
        $masterDevice = $rmsDeviceInstance->getMasterDevice();
        if (!$masterDevice) {
            error_log(sprintf('%s:%s masterDevice unknown', __FILE__, __LINE__));
            return null;
        }
        $mfg = $masterDevice->getManufacturer();
        if (!$mfg) {
            error_log(sprintf('%s:%s manufacturer unknown', __FILE__, __LINE__));
            return null;
        }

        $tonerService = new TonerService(null, $client['dealerId']);
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
            'rmsDeviceInstance'=>$rmsDeviceInstance,
        ]);
        if (empty($deviceNeedsToner)) {
            printf("NEW Device Needs Toner! %s <br>\n", $rmsDeviceInstance->getIpAddress());
            $deviceNeedsToner = new \MPSToolbox\Entities\DeviceNeedsTonerEntity();
            $deviceNeedsToner->setRmsDeviceInstance($rmsDeviceInstance);
            $deviceNeedsToner->setColor(TonerColorEntity::find($colorId));
            $deviceNeedsToner->setClient($rmsDeviceInstance->getClient());
            $deviceNeedsToner->setAssetId($rmsDeviceInstance->getAssetId());
            $deviceNeedsToner->setIpAddress($rmsDeviceInstance->getIpAddress());
            $deviceNeedsToner->setSerialNumber($rmsDeviceInstance->getSerialNumber());
            $deviceNeedsToner->setLocation($rmsDeviceInstance->getLocation());
            $deviceNeedsToner->setFirstReported(new \DateTime());
            $deviceNeedsToner->setMasterDevice($masterDevice);
            $deviceNeedsToner->setTonerOptions(implode(',', $tonerOptionIds));
            $deviceNeedsToner->setToner($toner);
            $result = true;
        } else {
            if ($deviceNeedsToner->getShopifyOrder()) {
                printf("Device Needs Toner update %s but toner has been ordered, order id: %s <br>\n", $rmsDeviceInstance->getIpAddress(), $deviceNeedsToner->getShopifyOrder());
            } else {
                $result = true;
                printf("Device Needs Toner update %s <br>\n", $rmsDeviceInstance->getIpAddress());
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

    public function syncToner($tonerId, $dealerId) {
        return file_get_contents('http://proxy.mpstoolbox.com/shopify/sync_toner.php?id='.$tonerId.'&dealerId='.$dealerId.'&origin='.$_SERVER['HTTP_HOST']);
    }

    /**
     * @param $c
     * @param $that RmsUpdateService
     * @throws \Zend_Mail_Exception
     */
    public function sendEmail($c, $that) {
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

        $emailFromAddress = $shopSettings->emailFromAddress;
        $emailFromName = $shopSettings->emailFromName;

        $rows = DeviceNeedsTonerEntity::getByClient($client);
        $arr=[];
        foreach ($rows as $device) {
            /** @var DeviceNeedsTonerEntity $device */
            $id = "{$device->getIpAddress()}-{$device->getSerialNumber()}-{$device->getAssetId()}";
            $arr[$id][] = $device;
        }

        $devicesByTemplate = [];
        $buynowByTemplate = [];
        foreach ($arr as $d) foreach ($d as $i=>$device) {
            $templateNum = $device->getRmsDeviceInstance()->getEmailTemplate();
            switch ($templateNum) {
                case 1: case 2: case 3: { break; }
                default: { $templateNum=$c['templateNum']; }
            }

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

            $priceLevelName = 'Base';
            $priceLevel = $client->getPriceLevel();
            if ($priceLevel) $priceLevelName = $priceLevel->getName();
            $json=json_decode($that->syncToner($device->getToner()->getId(), $dealerId),true);
            if (is_array($json)) {
                foreach ($json as $n=>$variantId) {
                    if ($n == $priceLevelName) {
                        if (isset($buynowByTemplate[$templateNum][$variantId])) $buynowByTemplate[$templateNum][$variantId]++;
                        else $buynowByTemplate[$templateNum][$variantId] = 1;
                    }
                }
            }

            if ($i==0) $devicesByTemplate[$templateNum][] =
                <<<HTML
                <p>
    Device Model: <b>{$model}</b><br>
    Serial Number: <b>{$device->getSerialNumber()}</b><br>
    IP Address: <b>{$device->getIpAddress()}</b><br>
    {$location}
    Toner Level {$color}: <b>{$device->getTonerLevel()}%</b><br>
    Estimated Monthly Page Volume: <b>{$ampv}</b><br>
    Estimated time left until toner reaches {$shopSettings->thresholdPercent}%: <b>{$device->getDaysLeft()}</b> days
</p>
HTML;
            else $devicesByTemplate[$templateNum][] =
                <<<HTML
                <p>
    Toner Level {$color}: <b>{$device->getTonerLevel()}%</b><br>
    Estimated Monthly Page Volume: <b>{$ampv}</b><br>
    Estimated time left until toner reaches {$shopSettings->thresholdPercent}%: <b>{$device->getDaysLeft()}</b> days
</p>
HTML;
        }

        foreach ($devicesByTemplate as $templateNum=>$devices) {

            $buynow = [];
            if (!empty($buynowByTemplate[$templateNum])) {
                foreach ($buynowByTemplate[$templateNum] as $variantId=>$qty) {
                    $buynow[] = "{$variantId}:{$qty}";
                }
            }

            switch($templateNum) {
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
            if (empty($supplyNotifySubject)) $supplyNotifySubject = 'Printing Supplies Order Requirements for {{companyName}}';
            if (empty($supplyNotifyMessage)) $supplyNotifyMessage = <<<HTML
<p>Hello {{clientName}}</p>

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

            $link = '<a href="'.sprintf('http://%s.myshopify.com/tools/mps?p=rms&origin=email', $shopSettings->shopifyName).'"><img alt="Go to your supply cupboard" title="Go to your supply cupboard" src="https://staging.mpstoolbox.com/img/SupplyCupboardButton.png"></a>';
            $buybtn = '<a href="https://'.$shopSettings->shopifyName.'.myshopify.com/cart/'.implode(',',$buynow).'/"><img alt="Buy Now" title="Buy Now" src="https://staging.mpstoolbox.com/img/BuyNowButton.png"></a>';

            $html = str_replace([
                '{{clientName}}',
                '{{contactName}}',
                '{{devices}}',
                '{{link}}',
                '{{buybtn}}',
            ], [
                $client->getCompanyName(),
                $contactName,
                implode("\n",$devices),
                $link,
                $buybtn
            ], $supplyNotifyMessage);

            $email = new \Zend_Mail();
            $email->setFrom($emailFromAddress, $emailFromName);
            $email->addTo(explode(',', str_replace(';', ',', empty($contact->emailSupply) ? $contact->email : $contact->emailSupply)));
            $email->addBcc($dealerBranding->dealerEmail);
            $email->setSubject(str_replace('{{clientName}}', $client->getCompanyName(), $supplyNotifySubject)); //'Printing Supplies Order Requirements for '.$client->getCompanyName());
            $email->setBodyHtml($html);
            $email->setBodyText(strip_tags($html));
            try {
                $email->send();
            } catch (\Exception $ex) {
                Logger::error($ex->getMessage(), $ex);
            }
        }
    }

    /**
     * @param array $devices
     * @param array $client
     * @param ShopSettingsEntity $settings
     */
    public function checkDevices($devices, $client, $settings) {
        $sendEmail = false;
        foreach ($devices as $device) {
            /** @var RmsUpdateEntity $device */

            $diff = date_diff($device->getMonitorStartDate(), $device->getMonitorEndDate());

            $color_meter = $device->getEndMeterColor() - $device->getStartMeterColor();
            $color_daily = $color_meter/$diff->days;

            $black_meter = $color_meter + ($device->getEndMeterBlack() - $device->getStartMeterBlack());
            $black_daily = $black_meter/$diff->days;

            if ($device->needsToner(TonerColorEntity::BLACK, $black_daily, $settings)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::BLACK);
            } else if ($device->getTonerLevelBlack()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::BLACK);
            }
            if ($device->needsToner(TonerColorEntity::MAGENTA, $color_daily, $settings)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::MAGENTA);
            } else if ($device->getTonerLevelMagenta()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::MAGENTA);
            }
            if ($device->needsToner(TonerColorEntity::CYAN, $color_daily, $settings)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::CYAN);
            } else if ($device->getTonerLevelCyan()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::CYAN);
            }
            if ($device->needsToner(TonerColorEntity::YELLOW, $color_daily, $settings)) {
                $sendEmail |= $this->deviceNeedsToner($device, $client, TonerColorEntity::YELLOW);
            } else if ($device->getTonerLevelYellow()>5) {
                $this->tonerMayBeReplaced($device, TonerColorEntity::YELLOW);
            }
        }

        if ($sendEmail) {
            printf('sending email...');
            $this->sendEmail($client, $this);
        }
    }

}