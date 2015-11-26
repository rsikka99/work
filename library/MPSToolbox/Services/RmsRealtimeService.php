<?php

namespace MPSToolbox\Services;

use MPSToolbox\Entities\ClientEntity;
use MPSToolbox\Entities\RmsDeviceInstanceEntity;
use MPSToolbox\Entities\RmsUpdateEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;

class RmsRealtimeService {

    /**
    public function findDeviceInstance($clientId, \SimpleXMLElement $device) {
        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare('select * from rms_device_instances where clientId=:clientId and assetId=:assetId and ipAddress=:ipAddress and serialNumber=:serialNumber');
        $assetId = $device->AssetNumber ? $device->AssetNumber : $device->MacAddress;
        $st->execute([':clientId'=>$clientId,':ipAddress'=>$device->IPAddress, ':serialNumber'=>$device->SerialNumber, ':assetId'=>$assetId]);
        $result = $st->fetch();
        if (!$result) {
            $st = $db->prepare('select * from rms_device_instances where clientId=:clientId and ipAddress=:ipAddress and serialNumber=:serialNumber and assetId=\'\'');
            $st->execute([':clientId'=>$clientId,':ipAddress'=>$device->IPAddress, ':serialNumber'=>$device->SerialNumber]);
            $result = $st->fetch();
        }
        return $result;
    }
    **/

    function processPrintauditXml($clientId, \SimpleXMLElement $xml) {

        /** @var ClientEntity $client */
        $client = ClientEntity::find($clientId);

        $rmsUpdateService = new RmsUpdateService();

        $db = \Zend_Db_Table::getDefaultAdapter();
        foreach ($xml->Device as $device) {

            $device_instance = RmsDeviceInstanceEntity::findOne($clientId, $device->IPAddress, $device->SerialNumber, $device->AssetNumber ? $device->AssetNumber : $device->MacAddress); //$this->findDeviceInstance($clientId, $device);
            if (!$device_instance) {
                echo "!!! {$device->Name} {$device->IPAddress} {$device->SerialNumber}<br>\n";
            }

            echo "{$device->Name} {$device->IPAddress} {$device->SerialNumber}<br>\n";

            $masterDeviceId = null;
            $masterDevice = null;
            $fullName = $device->Manufacturer.' '.$device->ModelName;
            if ($device_instance && $device_instance->getMasterDevice()) {
                $masterDevice = MasterDeviceMapper::getInstance()->find($device_instance->getMasterDevice()->getId());
            }

            if (!$masterDevice) {
                $masterDevice = $rmsUpdateService->tryToMap($device->Manufacturer, $device->ModelName);
            }

            if ($masterDevice) {
                $fullName = $masterDevice->getFullDeviceName();
                $masterDeviceId = $masterDevice->id;
            }


            $data=[
                'rmsDeviceInstanceId' => $device_instance ? $device_instance->getId() : null,
                'scanDate' => date('Y-m-d H:i:s', strtotime($device->ScanDateLocalized)),
                'clientId' => $clientId,
                'assetId' => $device->AssetNumber ? $device->AssetNumber : $device->MacAddress,
                'ipAddress' => $device->IPAddress,
                'serialNumber' => $device->SerialNumber,
                'rawDeviceName' => $device->Name,
                'fullDeviceName' => $fullName,
                'manufacturer' => $device->Manufacturer,
                'modelName' => $device->ModelName,
                'location' => $device->Location,
                'masterDeviceId' => $masterDeviceId,
                'rmsProviderId' => '4', //Printaudit
                'lifeCount' => $device->LifeCount,
                'lifeCountBlack' => $device->LifeCountMono,
                'lifeCountColor' => $device->LifeCountColor,
                'copyCountBlack' => $device->CopyCountMono,
                'copyCountColor' => $device->CopyCountColor,
                'printCountBlack' => $device->PrintCountMono,
                'printCountColor' => $device->PrintCountColor,
                'scanCount' => $device->ScanCount,
                'faxCount' => $device->FaxCount,
                'tonerLevelBlack' => $device->Toners->TonerBlack->Level,
                'tonerLevelCyan' => $device->Toners->TonerCyan->Level,
                'tonerLevelMagenta' => $device->Toners->TonerMagenta->Level,
                'tonerLevelYellow' => $device->Toners->TonerYellow->Level
            ];
            $data['rmsDeviceInstanceId'] = $rmsUpdateService->toDeviceInstance($data);
            $this->replaceRmsRealtime($data);
        }

        $sql="select distinct(scanDate) as scanDate from rms_realtime where scanDate>='".date('Y-m-d',strtotime('-365 DAY'))."' and rmsDeviceInstanceId in (select id from rms_device_instances where clientId=".intval($clientId).") order by scanDate";
        $st = $db->query($sql);
        $arr = $st->fetchAll();
        if (count($arr)>1) {
            $line = array_shift($arr);
            $startDate = $line['scanDate'];
            $line = array_pop($arr);
            $endDate = $line['scanDate'];
            if ($startDate && $endDate && ($startDate!=$endDate)) {
                $st = $db->query("select * from rms_realtime r join rms_device_instances i on r.rmsDeviceInstanceId=i.id where clientId={$clientId} and (scanDate='{$startDate}' or scanDate='{$endDate}') order by scanDate");
                $data=[];
                foreach ($st->fetchAll() as $line) {
                    $data[$line['rmsDeviceInstanceId']][$line['scanDate']] = $line;
                }
                foreach ($data as $rmsDeviceInstanceId=>$row) {
                    if (count($row)!=2) {
                        unset($data[$rmsDeviceInstanceId]);
                    }
                }
                $devices = [];
                foreach ($data as $rmsDeviceInstanceId=>$row) {
                    $from = array_shift($row);
                    $until = array_shift($row);
                    if (!$until['masterDeviceId']) continue;
                    $masterDevice = MasterDeviceMapper::getInstance()->find($until['masterDeviceId']);
                    if (!$masterDevice) continue;

                    $data = [
                        'rmsDeviceInstanceId'=>$rmsDeviceInstanceId,
                        'clientId'=>$until['clientId'],
                        'assetId'=>$until['assetId'],
                        'ipAddress'=>$until['ipAddress'],
                        'serialNumber'=>$until['serialNumber'],
                        'location'=>$until['location'],
                        'rawDeviceName'=>$until['rawDeviceName'],
                        'masterDeviceId'=>$until['masterDeviceId'],
                        'rmsProviderId'=>$until['rmsProviderId'],
                        'isColor'=>$masterDevice->isColor,
                        'isCopier'=>$masterDevice->isCopier,
                        'isFax'=>$masterDevice->isFax,
                        'isLeased'=>$masterDevice->isLeased($client->getDealer()->getId()),
                        'isDuplex'=>$masterDevice->isDuplex,
                        'isA3'=>$masterDevice->isA3,
                        'reportsTonerLevels'=>$masterDevice->isCapableOfReportingTonerLevels,
                        'launchDate'=>$masterDevice->launchDate,
                        'ppmBlack'=>$masterDevice->ppmBlack,
                        'ppmColor'=>$masterDevice->ppmColor,
                        'wattsPowerNormal'=>$masterDevice->wattsPowerNormal,
                        'wattsPowerIdle'=>$masterDevice->wattsPowerIdle,
                        'tonerLevelBlack'=>$until['tonerLevelBlack'],
                        'tonerLevelCyan'=>$until['tonerLevelCyan'],
                        'tonerLevelMagenta'=>$until['tonerLevelMagenta'],
                        'tonerLevelYellow'=>$until['tonerLevelYellow'],
                        #'pageCoverageMonochrome',
                        #'pageCoverageCyan',
                        #'pageCoverageMagenta',
                        #'pageCoverageYellow',
                        #'pageCoverageColor',
                        #'mpsDiscoveryDate',
                        #'isManaged',
                        'monitorStartDate'=>$from['scanDate'],
                        'monitorEndDate'=>$until['scanDate'],
                        'startMeterBlack'=>$from['lifeCountBlack'],
                        'endMeterBlack'=>$until['lifeCountBlack'],
                        'startMeterColor'=>$from['lifeCountColor'],
                        'endMeterColor'=>$until['lifeCountColor'],
                        'startMeterPrintBlack'=>$from['printCountBlack'],
                        'endMeterPrintBlack'=>$until['printCountBlack'],
                        'startMeterPrintColor'=>$from['printCountColor'],
                        'endMeterPrintColor'=>$until['printCountColor'],
                        'startMeterCopyBlack'=>$from['copyCountBlack'],
                        'endMeterCopyBlack'=>$until['copyCountBlack'],
                        'startMeterCopyColor'=>$from['copyCountColor'],
                        'endMeterCopyColor'=>$until['copyCountColor'],
                        'startMeterFax'=>$from['faxCount'],
                        'endMeterFax'=>$until['faxCount'],
                        'startMeterScan'=>$from['scanCount'],
                        'endMeterScan'=>$until['scanCount'],
                        #'startMeterPrintA3Black'
                        #'endMeterPrintA3Black',
                        #'startMeterPrintA3Color',
                        #'endMeterPrintA3Color',
                        'startMeterLife'=>$from['lifeCount'],
                        'endMeterLife'=>$until['lifeCount'],
                    ];
                    $rmsUpdateService->replaceRmsUpdate($data);

                    //$instance = RmsDeviceInstanceEntity::find($rmsDeviceInstanceId);
                    $devices[] = RmsUpdateEntity::find($rmsDeviceInstanceId);
                }
                $rmsUpdateService->checkDevices($devices, $client);
            }
        }
    }

    public function replaceRmsRealtime($data) {
        $fields = ['rmsDeviceInstanceId','scanDate','rmsProviderId','lifeCount','lifeCountBlack',
            'lifeCountColor','copyCountBlack','copyCountColor','printCountBlack','printCountColor','scanCount','faxCount','tonerLevelBlack','tonerLevelCyan','tonerLevelMagenta','tonerLevelYellow'];

        $str1='`'.implode('`,`',$fields).'`';
        $str2=':'.implode(',:',$fields);

        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare("replace into rms_realtime ( {$str1} ) values ( {$str2} )");
        foreach ($fields as $field) if (!isset($data[$field])) $data[$field]=null;
        foreach ($data as $key=>$value) if (!in_array($key, $fields)) unset($data[$key]);

        $st->execute($data);
    }

}