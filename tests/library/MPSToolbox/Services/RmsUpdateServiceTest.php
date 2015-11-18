<?php

/**
class MockedMessage extends \Fetch\Message {
    public function __construct($subject) {
        $this->subject = $subject;
    }
}
/**/

class RmsUpdateServiceTest extends My_DatabaseTestCase {

    public $fixtures = [
        'clients',
        'dealer_settings',
        'rms_providers',
        'rms_uploads',
        'device_instances',
        'master_devices',
        'device_instance_master_devices',
        'manufacturers',
        'toners',
        'device_toners',
        'toner_vendor_rankings',
        //'toner_vendor_ranking_sets',
        'fleet_settings',
        'generic_settings',
        'optimization_settings',
        'quote_settings',
        'dealer_settings',
        'client_settings',
        'dealer_branding',
        'contacts'
    ];

    public function test_replaceRmsUpdate() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $service->replaceRmsUpdate([
            'clientId'=>'1',
            'assetId'=>'123',
            'ipAddress'=>'12.12.12.12',
            'serialNumber'=>'321',
            'rmsProviderId'=>'6',
        ]);
    }

    public function test_findDeviceInstance() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $line = $service->findDeviceInstance(5, ['ipAddress'=>'172.16.3.191', 'serialNumber'=>'J0J747067']);
        $this->assertEquals(2, $line['masterDeviceId']);
    }

    public function test_getRmsClients() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $result = $service->getRmsClients();
        $this->assertEquals(1, count($result));

    }

    public function test_update() {
        $http = $this->getMock('\cdyweb\http\BaseAdapter');
        $json1 = '
[
    {
        "groupId": "14f27877-e667-42b0-a2de-06d17f1173a0",
        "lastReportedAt": "2015-10-28T16:49:52Z",
        "firstReportedAt": "2015-05-28T18:06:54.85Z",
        "status": "Ok",
        "type": "Network",
        "ipAddress": "172.16.3.191",
        "macAddress": "EC-B1-D7-F7-E6-C3",
        "managementStatus": "Managed",
        "licenseStatus": "Full",
        "location": "St. Martin Catholic School",
        "serialNumber": "J0J747067",
        "modelMatch": {
            "type": "AutoMany",
            "modifiedAt": "2015-07-22T21:07:20.337Z",
            "model": {
                "id": "8d7b6a21-8044-4528-9aa0-317b64d6d7a5",
                "name": "Hewlett-Packard Officejet Pro X476dw",
                "manufacturer": "Hewlett-Packard",
                "isColor": true,
                "hasImage": true
            },
            "isAutoMatchEnabled": true
        },
        "id": "5d9dd066-9475-4405-a121-1b96c1073bab",
        "name": "HP Officejet Pro X476dw MFP"
    }
]';
        $json2 = '
{
  "dataNodeId": "23af3444-0f0d-412f-bcb8-dc6a5c254915",
  "lastReportedAt": "2015-10-28T19:06:23Z",
  "firstReportedAt": "2015-05-28T18:06:54.85Z",
  "status": "Ok",
  "type": "Network",
  "ipAddress": "192.168.17.44",
  "macAddress": "EC-B1-D7-F7-E6-C3",
  "commonLifecountMeters": {
    "total": {
      "count": 4450,
      "delta": 668,
      "sparkline": [
        18,
        0
      ],
      "firstReportedAt": "2015-05-28T18:06:16Z",
      "lastReportedAt": "2015-10-28T19:06:23Z"
    },
    "mono": {
      "count": 1263,
      "delta": 233,
      "sparkline": [
        5,
        0
      ],
      "firstReportedAt": "2015-05-28T18:06:16Z",
      "lastReportedAt": "2015-10-28T19:06:23Z"
    },
    "color": {
      "count": 3181,
      "delta": 435,
      "sparkline": [
        13,
        0
      ],
      "firstReportedAt": "2015-05-28T18:06:16Z",
      "lastReportedAt": "2015-10-28T19:06:23Z"
    }
  },
  "colorSupplies": {
    "black": {
      "id": "59ebc564-b2de-481c-9b51-d1d8e3b0bc8c",
      "label": "Black Marker Level",
      "color": "Black",
      "type": "Toner",
      "rfcType": "Toner",
      "oemPartNumber": "CN621A",
      "level": {
        "status": "Numeric",
        "highPercent": 80,
        "lowPercent": 80,
        "firstReportedAt": "2015-05-28T18:06:16Z",
        "lastReportedAt": "2015-10-28T19:06:23Z"
      }
    },
    "cyan": {
      "id": "16bea5a3-4102-4ba4-9b1e-1c3454c20979",
      "label": "Cyan Marker Level",
      "color": "Cyan",
      "type": "Toner",
      "rfcType": "Toner",
      "oemPartNumber": "CN622A",
      "level": {
        "status": "Numeric",
        "highPercent": 95,
        "lowPercent": 95,
        "firstReportedAt": "2015-05-28T18:06:16Z",
        "lastReportedAt": "2015-10-28T19:06:23Z"
      }
    },
    "magenta": {
      "id": "4f5c7315-ce3f-4234-9243-35c42f0ea5ac",
      "label": "Magenta Marker Level",
      "color": "Magenta",
      "type": "Toner",
      "rfcType": "Toner",
      "oemPartNumber": "CN623A",
      "level": {
        "status": "Numeric",
        "highPercent": 93,
        "lowPercent": 93,
        "firstReportedAt": "2015-05-28T18:06:16Z",
        "lastReportedAt": "2015-10-28T19:06:23Z"
      }
    },
    "yellow": {
      "id": "04f757f4-da52-4245-aede-f1995a5bd624",
      "label": "Yellow Marker Level",
      "color": "Yellow",
      "type": "Toner",
      "rfcType": "Toner",
      "oemPartNumber": "CN624A",
      "level": {
        "status": "Numeric",
        "highPercent": 79,
        "lowPercent": 79,
        "firstReportedAt": "2015-05-28T18:06:16Z",
        "lastReportedAt": "2015-10-28T19:06:23Z"
      }
    }
  },
  "utilization": {
    "percent": 1,
    "source": "Calculated",
    "modifiedAt": "2015-10-27T22:00:22.83Z"
  },
  "coverages": {
    "mono": {
      "source": "Calculated",
      "modifiedAt": "2015-10-28T17:01:10.85Z"
    },
    "color": {
      "source": "Calculated",
      "modifiedAt": "2015-10-28T17:01:10.85Z"
    },
    "total": {
      "source": "Calculated",
      "modifiedAt": "2015-10-28T17:01:10.85Z"
    }
  },
  "activeCodesCount": 6,
  "activeAlertsCount": 0,
  "isRemoteConfigEnabled": false,
  "access": {
    "canUpdateName": true,
    "canUpdateModelMatch": true,
    "canUpdateLicenseStatus": true,
    "canUpdateManagementStatus": true,
    "canUpdateLocation": true,
    "canUpdateAssetNumber": true,
    "canUpdateSerialNumber": true,
    "canUpdateCustomFieldValues": true,
    "canUpdateRemoteConfig": false
  },
  "breadcrumbGroups": [
    {
      "id": "3bdc3992-f30b-4a27-ba5f-b7dcb03a16d5",
      "name": "RENT THE PRINTER.COM>16107>CAN"
    },
    {
      "id": "3947dff2-f32c-48c2-aa5b-5652e244639a",
      "name": "Compass Early Learning and Care"
    },
    {
      "id": "14f27877-e667-42b0-a2de-06d17f1173a0",
      "name": "Ennismore"
    }
  ],
  "nameFromTemplate": "HP Officejet Pro X476dw MFP",
  "id": "5d9dd066-9475-4405-a121-1b96c1073bab",
  "name": {
    "value": "HP Officejet Pro X476dw MFP",
    "deviceValue": "HP Officejet Pro X476dw MFP",
    "source": "Device"
  },
  "groupId": "14f27877-e667-42b0-a2de-06d17f1173a0",
  "managementStatus": "Managed",
  "licenseStatus": "Full",
  "location": {
    "value": "St. Martin Catholic School",
    "deviceValue": "Compass Shamrock - Admin Office",
    "source": "User"
  },
  "serialNumber": {
    "value": "CN51KJK0G5",
    "deviceValue": "CN51KJK0G5",
    "source": "Device"
  },
  "hostname": {
    "value": "HPF7E6C3",
    "deviceValue": "HPF7E6C3",
    "source": "Device"
  },
  "modelMatch": {
    "type": "AutoMany",
    "modifiedAt": "2015-07-22T21:07:20.337Z",
    "model": {
      "id": "8d7b6a21-8044-4528-9aa0-317b64d6d7a5",
      "name": "Hewlett-Packard Officejet Pro X476dw",
      "manufacturer": "Hewlett-Packard",
      "isColor": true,
      "hasImage": true
    },
    "isAutoMatchEnabled": true
  },
  "customFields": []
}
';
        $json3 = '
[
  {
    "label": "PRINTTOTAL",
    "count": 1195,
    "delta": 43,
    "sparkline": [
      19,
      0,
      24
    ],
    "firstReportedAt": "2015-09-26T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "SCAN",
    "count": 163,
    "delta": 9,
    "sparkline": [
      0,
      0,
      9
    ],
    "firstReportedAt": "2015-09-25T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "SCAN_SENDJOB",
    "count": 26,
    "delta": 2,
    "sparkline": [
      0,
      0,
      2
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "COPIERTOTAL",
    "count": 217,
    "delta": 6,
    "sparkline": [
      0,
      0,
      6
    ],
    "firstReportedAt": "2015-09-25T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "SCAN_COPYJOB",
    "count": 137,
    "delta": 7,
    "sparkline": [
      0,
      0,
      7
    ],
    "firstReportedAt": "2015-09-25T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "LETTERCOLORDUPLEX",
    "count": 129,
    "delta": 0,
    "sparkline": [
      0,
      0,
      0
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-01T00:00:00Z"
  },
  {
    "label": "LETTERMONODUPLEX",
    "count": 195,
    "delta": 3,
    "sparkline": [
      0,
      0,
      3
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "FAXMONO",
    "count": 11,
    "delta": 0,
    "sparkline": [
      0,
      0,
      0
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-01T00:00:00Z"
  },
  {
    "label": "LEGALCOLORDUPLEX",
    "count": 2,
    "delta": 0,
    "sparkline": [
      0,
      0,
      0
    ],
    "firstReportedAt": "2015-09-01T00:00:00Z",
    "lastReportedAt": "2015-10-01T00:00:00Z"
  },
  {
    "label": "LEGALMONODUPLEX",
    "count": 5,
    "delta": 0,
    "sparkline": [
      0,
      0,
      0
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-01T00:00:00Z"
  },
  {
    "label": "LEGALCOLORSIMPLEX",
    "count": 2,
    "delta": 0,
    "sparkline": [
      0,
      0,
      0
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-01T00:00:00Z"
  },
  {
    "label": "LEGALMONOSIMPLEX",
    "count": 2,
    "delta": 0,
    "sparkline": [
      0,
      0,
      0
    ],
    "firstReportedAt": "2015-09-17T00:00:00Z",
    "lastReportedAt": "2015-10-01T00:00:00Z"
  },
  {
    "label": "LETTERCOLORSIMPLEX",
    "count": 813,
    "delta": 26,
    "sparkline": [
      7,
      0,
      19
    ],
    "firstReportedAt": "2015-09-26T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "LETTERMONOSIMPLEX",
    "count": 1113,
    "delta": 44,
    "sparkline": [
      19,
      0,
      25
    ],
    "firstReportedAt": "2015-09-26T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "Total Units Output",
    "count": 1423,
    "delta": 49,
    "sparkline": [
      19,
      0,
      30
    ],
    "firstReportedAt": "2015-09-26T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "Total Mono Units Output",
    "count": 472,
    "delta": 23,
    "sparkline": [
      12,
      0,
      11
    ],
    "firstReportedAt": "2015-09-25T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  },
  {
    "label": "Total Color Units Output",
    "count": 946,
    "delta": 26,
    "sparkline": [
      7,
      0,
      19
    ],
    "firstReportedAt": "2015-09-26T00:00:00Z",
    "lastReportedAt": "2015-10-02T00:00:00Z"
  }
]
        ';

        $http->expects($this->exactly(3))->method('get')->will(
            $this->onConsecutiveCalls(
                new \cdyweb\http\psr\Response(200, [], $json1),
                new \cdyweb\http\psr\Response(200, [], $json2),
                new \cdyweb\http\psr\Response(200, [], $json3)
            )
        );

        $service = new \MPSToolbox\Services\RmsUpdateService();
        $service->setHttp($http);
        $clients = $service->getRmsClients();
        $client=current($clients);
        $service->update($client['clientId'], $client['rmsUri'], $client['deviceGroup']);
    }

    public function test_deviceNeedsToner() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $device = new \MPSToolbox\Entities\RmsUpdateEntity();
        $device->setClient(\MPSToolbox\Entities\ClientEntity::find(5));
        $device->setAssetId('123');
        $device->setIpAddress('1.1.1.1');
        $device->setSerialNumber('321');
        $device->setLocation('here and there');
        $device->setMasterDevice(\MPSToolbox\Entities\MasterDeviceEntity::find(1));
        $device->setTonerLevelBlack(34);
        $device->setTonerLevelMagenta(23);
        $device->setDaysLeft([
            \MPSToolbox\Entities\TonerColorEntity::BLACK=>4,
            \MPSToolbox\Entities\TonerColorEntity::MAGENTA=>5,
        ]);
        $client = ['clientId'=>5, 'dealerId'=>1];
        $service->deviceNeedsToner($device, $client, \MPSToolbox\Entities\TonerColorEntity::BLACK);
        $service->deviceNeedsToner($device, $client, \MPSToolbox\Entities\TonerColorEntity::MAGENTA);
    }

    public function test_tonerMayBeReplaced() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        $device = new \MPSToolbox\Entities\RmsUpdateEntity();
        $device->setClient(\MPSToolbox\Entities\ClientEntity::find(5));
        $device->setAssetId('123');
        $device->setIpAddress('1.1.1.1');
        $device->setSerialNumber('321');
        $device->setLocation('here and there');
        $device->setMasterDevice(\MPSToolbox\Entities\MasterDeviceEntity::find(1));
        $device->setTonerLevelBlack(34);
        $device->setDaysLeft([\MPSToolbox\Entities\TonerColorEntity::BLACK=>4]);
        $client = ['clientId'=>5, 'dealerId'=>1];
        $service->deviceNeedsToner($device, $client, \MPSToolbox\Entities\TonerColorEntity::BLACK);

        $device->setTonerLevelBlack(99);
        $service->tonerMayBeReplaced($device, \MPSToolbox\Entities\TonerColorEntity::BLACK);
    }


    public function test_sendEmail() {
        $service = new \MPSToolbox\Services\RmsUpdateService();
        #--
        $device = new \MPSToolbox\Entities\RmsUpdateEntity();
        $device->setClient(\MPSToolbox\Entities\ClientEntity::find(5));
        $device->setAssetId('123');
        $device->setIpAddress('1.1.1.1');
        $device->setSerialNumber('321');
        $device->setLocation('here and there');
        $device->setMasterDevice(\MPSToolbox\Entities\MasterDeviceEntity::find(1));
        $device->setTonerLevelBlack(34);
        $device->setTonerLevelMagenta(23);
        $device->setDaysLeft([
            \MPSToolbox\Entities\TonerColorEntity::BLACK=>4,
            \MPSToolbox\Entities\TonerColorEntity::MAGENTA=>5,
        ]);
        $client = ['clientId'=>5, 'dealerId'=>1];
        #--
        $db = \Zend_Db_Table::getDefaultAdapter();
        $db->query("
replace into rms_update set
    clientId=5,
    assetId='123',
    ipAddress='1.1.1.1',
    serialNumber='321',
    rmsProviderId=6,
    masterDeviceId=1,
    monitorStartDate='2015-01-01',
    monitorEndDate='2015-02-02',
    startMeterBlack='100',
    endMeterBlack='500',
    startMeterColor='200',
    endMeterColor='750'
")->execute();
        #--
        $service->deviceNeedsToner($device, $client, \MPSToolbox\Entities\TonerColorEntity::BLACK);
        $service->deviceNeedsToner($device, $client, \MPSToolbox\Entities\TonerColorEntity::MAGENTA);
        #--
        $service->sendEmail(['clientId'=>5]);
    }
}