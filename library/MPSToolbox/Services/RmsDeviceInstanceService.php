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
  select printing_device from oem_printing_device_consumable
)
and `ignore`=0
and (a.isLeased is null or a.isLeased=0)
and {$where}
        ");
        foreach ($st->fetchAll() as $line) {
            $result[$line['id']]=$line['id'];
        }

        $sql = "
select di.id, md.tonerConfigId, t.colorId, v1.cost
from rms_device_instances di
  join base_printer md on di.masterDeviceId=md.id
  join oem_printing_device_consumable dt on dt.printing_device=md.id
  join base_printer_cartridge t on dt.printer_consumable=t.id
  left join dealer_toner_attributes a on t.id=a.tonerId and a.dealerId={$dealerId}
  left join (select min(cost) as cost, dist, tonerId, dealerId from _view_dist_stock_price group by tonerId, dealerId) as v1 on v1.tonerId=t.id and v1.dealerId={$dealerId}
where {$where} and `ignore`=0 and t.colorId in (1,2,3,4)
        ";
        $st = $db->query($sql);

        $colorDevices = [];
        $monoDevices = [];
        foreach ($st->fetchAll() as $line) {
            if ($line['tonerConfigId'] == 1) { //monochrome
                $monoDevices[$line['id']][] = $line;
            } else {
                $colorDevices[$line['id']][$line['colorId']][] = $line;
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