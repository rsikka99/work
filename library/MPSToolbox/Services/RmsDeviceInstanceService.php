<?php

namespace MPSToolbox\Services;

use MPSToolbox\Legacy\Entities\DealerEntity;

class RmsDeviceInstanceService {
    public function getIncomplete($clientId=null, $dealerId=null) {
        $result = [];
        if (!$dealerId) {
            $dealerId = DealerEntity::getDealerId();
        }

        $where="di.clientId={$clientId}";
        if (!$clientId) $where="di.clientId in (select id from clients where dealerId={$dealerId})";

        $db = \Zend_Db_Table::getDefaultAdapter();

        $st = $db->query("select id from rms_device_instances di where masterDeviceId is null and `ignore`=0 and {$where}");
        foreach ($st->fetchAll() as $line) {
            $result[$line['id']]=$line['id'];
        }
        $st = $db->query("
select di.id from rms_device_instances di
  left join dealer_master_device_attributes a on a.masterDeviceId=di.masterDeviceId and a.dealerId={$dealerId}
where di.masterDeviceId not in (
  select master_device_id
  from device_toners dt
    join master_devices msub on dt.master_device_id=msub.id
    join toners t on dt.toner_id=t.id and t.manufacturerId = msub.manufacturerId
)
and `ignore`=0
and (a.isLeased is null or a.isLeased=0)
and {$where}
        ");
        foreach ($st->fetchAll() as $line) {
            $result[$line['id']]=$line['id'];
        }

        $sql = "
select di.id, md.tonerConfigId, t.tonerColorId, v1.cost
from rms_device_instances di
  join master_devices md on di.masterDeviceId=md.id
  join device_toners dt on dt.master_device_id=md.id
  join toners t on dt.toner_id=t.id
  left join dealer_toner_attributes a on t.id=a.tonerId and a.dealerId={$dealerId}
  left join _view_dist_stock_price_grouped v1 on t.id=v1.tonerId and v1.dealerId={$dealerId}
where {$where} and `ignore`=0
        ";
        $st = $db->query($sql);

        $colorDevices = [];
        $monoDevices = [];
        foreach ($st->fetchAll() as $line) {
            if ($line['tonerConfigId'] == 1) { //monochrome
                $monoDevices[$line['id']][] = $line;
            } else {
                $colorDevices[$line['id']][$line['tonerColorId']][] = $line;
            }
        }
        foreach ($monoDevices as $id=>$arr) {
            $valid=false;
            foreach ($arr as $line) {
                if ($line['cost']) {
                    $valid=true;
                }
            }
            if (!$valid) {
                $result[$id] = $id;
            }
        }
        foreach ($colorDevices as $id=>$arr) {
            $colorArr=[];
            foreach ($arr as $tonerColorId=>$lines) {
                foreach ($lines as $line) {
                    if ($line['cost']) {
                        $colorArr[$tonerColorId] = $line;
                    }
                }
            }
            if (count($colorArr)!=4) {
                $result[$id] = $id;
            }
        }

        return $result;
    }
}