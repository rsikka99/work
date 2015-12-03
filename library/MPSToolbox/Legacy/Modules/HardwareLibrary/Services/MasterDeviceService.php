<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Services;

use MPSToolbox\Legacy\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\HardwareLibrary\Validators\MasterDeviceValidator;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\MasterDeviceMapper;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use \Exception;

/**
 * Class MasterDeviceService
 *
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Services
 */
class MasterDeviceService
{
    /**
     * Deletes a master device
     *
     * @param $masterDeviceId
     *
     * @return int
     * @Deprecated
     */
    public function deleteMasterDevice ($masterDeviceId)
    {
        throw new Exception('Deprecated');
    }

    /**
     * Finds a master device
     *
     * @param $id
     *
     * @return MasterDeviceModel
     * @Deprecated
     */
    public function findMasterDevice ($id)
    {
        throw new Exception('Deprecated');
    }

    /**
     * @param      $data
     * @param null $id
     *
     * @return int|string
     * @Deprecated
     */
    public function saveMasterDevice ($data, $id = null)
    {
        throw new Exception('Deprecated');
    }

    /**
     * Searches for a master device and returns a list of matches
     *
     * @param $searchTerm
     *
     * @return array
     * @Deprecated
     */
    public function searchForMasterDevice ($searchTerm)
    {
        throw new Exception('Deprecated');
    }

    public function getIncomplete($clientId, $dealerId=null) {
        $result = [];
        if (!$dealerId) {
            $dealerId = DealerEntity::getDealerId();
        }

        $db = \Zend_Db_Table::getDefaultAdapter();
        $st = $db->query("
select m.id from master_devices m
  left join dealer_master_device_attributes a on a.masterDeviceId=m.id and a.dealerId={$dealerId}
where m.id not in (
  select master_device_id
  from device_toners dt
    join master_devices msub on dt.master_device_id=msub.id
    join toners t on dt.toner_id=t.id and t.manufacturerId = msub.manufacturerId
)
and (a.isLeased is null or a.isLeased=0)
        ");
        foreach ($st->fetchAll() as $line) {
            $result[$line['id']]=$line['id'];
        }
        $sql = "
select md.id, md.tonerConfigId, t.tonerColorId, a.cost, ingram_prices.customer_price as ingramPrice from master_devices md
  join device_toners dt on dt.master_device_id=md.id
  join toners t on dt.toner_id=t.id
  left join dealer_toner_attributes a on t.id=a.tonerId and a.dealerId={$dealerId}
  left join ingram_products on ingram_products.tonerId = t.id
  left join ingram_prices on ingram_prices.ingram_part_number = ingram_products.ingram_part_number
where md.id in (select masterDeviceId from rms_device_instances where clientId={$clientId})
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
                if ($line['cost'] || $line['ingramPrice']) {
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
                    if ($line['cost'] || $line['ingramPrice']) {
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
