<?php

namespace MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement;

use MPSToolbox\Entities\DealerEntity;
use MPSToolbox\Legacy\Modules\ProposalGenerator\Models\MasterDeviceModel;
use MPSToolbox\Legacy\Modules\QuoteGenerator\Mappers\DeviceMapper;
use My_Brand;
use My_Validate_DateTime;
use Zend_Form;

/**
 * @package MPSToolbox\Legacy\Modules\HardwareLibrary\Forms\DeviceManagement
 */
class DistributorsForm extends \My_Form_Form
{
    public function __construct ($options = null, $isAllowedToEditFields = false)
    {
        parent::__construct($options);
    }

    public function init ()
    {
    }

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/device-management/distributors-form.phtml']]]);
    }

    public static function getDistributors($masterDevice) {
        /** @var MasterDeviceModel $masterDevice */
        $distributors=[];
        #--
        if ($masterDevice) {
            $dealerId = \Zend_Auth::getInstance()->getIdentity()->dealerId;
            $device = DeviceMapper::getInstance()->find([$masterDevice->id, $dealerId]);
            if ($device) {
                $dealer = \MPSToolbox\Legacy\Mappers\DealerMapper::getInstance()->find($dealerId);
                $distributors[] = [
                    'name' => /* $attr->distributor ? $attr->distributor : */ $dealer->dealerName,
                    'sku' => $device->dealerSku,
                    'price' => $device->cost,
                    'stock' => '',
                ];
            }
        }
        $dealerId = DealerEntity::getDealerId();
        #--
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select * from ingram_products p join ingram_prices c using (ingram_part_number) where dealerId='.$dealerId.' and masterDeviceId=:masterDeviceId');
        $st->execute(['masterDeviceId'=>$masterDevice->id]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>'Ingram Micro',
                'sku'=>$line['ingram_part_number'],
                'price'=>$line['customer_price'],
                'stock'=>$line['availability_flag'],
            ];
        }
        #--
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select * from synnex_products p join synnex_prices c using (SYNNEX_SKU) where dealerId='.$dealerId.' and masterDeviceId=:masterDeviceId');
        $st->execute(['masterDeviceId'=>$masterDevice->id]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>'Synnex',
                'sku'=>$line['SYNNEX_SKU'],
                'price'=>$line['Unit_Cost'],
                'stock'=>$line['Qty_on_Hand'],
            ];
        }
        #--
        return $distributors;
    }
    public static function getServices(MasterDeviceModel $masterDevice)
    {
        $db = \Zend_Db_Table::getDefaultAdapter();
        return $db->query(
'
  select
    master_device_service.id,
    suppliers.name as supplier,
    COALESCE(ingram_products.vendor_part_number) as sku,
    COALESCE(ingram_products.ingram_part_number) as part_number,
    COALESCE(ingram_prices.customer_price) as price
  from
    master_device_service
    join suppliers on master_device_service.supplier=suppliers.id
    left join ingram_products on ingram_products.ingram_part_number =  master_device_service.ingram_part_number
      left join ingram_prices on ingram_prices.ingram_part_number = ingram_products.ingram_part_number
  WHERE
    master_device_service.masterDeviceId = ?
',
        [$masterDevice->id])->fetchAll();
    }

}