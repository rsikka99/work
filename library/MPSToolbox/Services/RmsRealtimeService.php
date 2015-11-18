<?php

namespace MPSToolbox\Services;

use MPSToolbox\Entities\ClientEntity;
use MPSToolbox\Entities\RmsUpdateEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;

class RmsRealtimeService {

    public function findDeviceInstance($clientId, \SimpleXMLElement $device) {
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
        $st->execute([':clientId'=>$clientId,':ipAddress'=>$device->IPAddress, ':serialNumber'=>$device->SerialNumber]);
        return $st->fetch(\PDO::FETCH_ASSOC);
    }

    function processPrintauditXml($clientId, \SimpleXMLElement $xml) {

        /** @var ClientEntity $client */
        $client = ClientEntity::find($clientId);
        $ignoreMasterDevices = explode(',',$client->getNotSupportedMasterDevices());

        $db = \Zend_Db_Table::getDefaultAdapter();
        foreach ($xml->Device as $device) {

            $device_instance = $this->findDeviceInstance($clientId, $device);
            if (!$device_instance) {
                echo "!!! {$device->Name} {$device->IPAddress} {$device->SerialNumber}<br>\n";
            }
            #if ($device_instance && in_array($device_instance['masterDeviceId'], $ignoreMasterDevices)) {
            #    echo "ignoring: {$device->Name} {$device->IPAddress} {$device->SerialNumber}<br>\n";
            #    continue;
            #}

            echo "{$device->Name} {$device->IPAddress} {$device->SerialNumber}<br>\n";

            $sql =
                '
replace into rms_realtime SET
  scanDate=:scanDate,
  clientId=:clientId,
  assetId=:assetId,
  ipAddress=:ipAddress,
  serialNumber=:serialNumber,
  rawDeviceName=:rawDeviceName,
  fullDeviceName=:fullDeviceName,
  manufacturer=:manufacturer,
  modelName=:modelName,
  location=:location,
  masterDeviceId=:masterDeviceId,
  rmsProviderId=:rmsProviderId,
  lifeCount=:lifeCount,
  lifeCountBlack=:lifeCountBlack,
  lifeCountColor=:lifeCountColor,
  copyCountBlack=:copyCountBlack,
  copyCountColor=:copyCountColor,
  printCountBlack=:printCountBlack,
  printCountColor=:printCountColor,
  scanCount=:scanCount,
  faxCount=:faxCount,
  tonerLevelBlack=:tonerLevelBlack,
  tonerLevelCyan=:tonerLevelCyan,
  tonerLevelMagenta=:tonerLevelMagenta,
  tonerLevelYellow=:tonerLevelYellow
';
            $db->prepare($sql)->execute([
                'scanDate' => date('Y-m-d H:i:s', strtotime($device->ScanDateLocalized)),
                'clientId' => $clientId,
                'assetId' => $device->AssetNumber ? $device->AssetNumber : $device->MacAddress,
                'ipAddress' => $device->IPAddress,
                'serialNumber' => $device->SerialNumber,
                'rawDeviceName' => $device->Name,
                'fullDeviceName' => $device->Manufacturer . ' ' . $device->ModelName,
                'manufacturer' => $device->Manufacturer,
                'modelName' => $device->ModelName,
                'location' => $device->Location,
                'masterDeviceId' => $device_instance ? $device_instance['masterDeviceId'] : null,
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
            ]);
        }

        $rmsUpdateService = new RmsUpdateService();
        $sql="select distinct(scanDate) as scanDate from rms_realtime where scanDate>='".date('Y-m-d',strtotime('-365 DAY'))."' and clientId=".intval($clientId)." order by scanDate";
        $st = $db->query($sql);
        $arr = $st->fetchAll();
        if (count($arr)>1) {
            $line = array_shift($arr);
            $startDate = $line['scanDate'];
            $line = array_pop($arr);
            $endDate = $line['scanDate'];
            if ($startDate && $endDate && ($startDate!=$endDate)) {
                $st = $db->query("select * from rms_realtime where clientId={$clientId} and (scanDate='{$startDate}' or scanDate='{$endDate}') order by scanDate");
                $data=[];
                foreach ($st->fetchAll() as $line) {
                    $id = "{$line['assetId']}-{$line['ipAddress']}-{$line['serialNumber']}";
                    $data[$id][$line['scanDate']] = $line;
                }
                foreach ($data as $id=>$row) {
                    if (count($row)!=2) {
                        unset($data[$id]);
                    }
                }
                $devices = [];
                foreach ($data as $row) {
                    $from = array_shift($row);
                    $until = array_shift($row);
                    if (!$until['masterDeviceId']) continue;
                    $masterDevice = MasterDeviceMapper::getInstance()->find($until['masterDeviceId']);
                    if (!$masterDevice) continue;

                    $data = [
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

                    $devices[] = RmsUpdateEntity::find([
                        'client' => $clientId,
                        'assetId' => $until['assetId'],
                        'ipAddress' => $until['ipAddress'],
                        'serialNumber' => $until['serialNumber']
                    ]);
                }
                $rmsUpdateService->checkDevices($devices, $client);
            }
        }
    }

}