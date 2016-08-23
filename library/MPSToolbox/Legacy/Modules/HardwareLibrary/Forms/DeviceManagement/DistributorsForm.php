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
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select s.name as supplier_name, supplierSku, price, isStock from supplier_product p join suppliers s on p.supplierId=s.id join supplier_price c using (supplierId, supplierSku) where dealerId='.$dealerId.' and baseProductId=?');
        $st->execute([$masterDevice->id]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>$line['supplier_name'],
                'sku'=>$line['supplierSku'],
                'price'=>$line['price'],
                'stock'=>$line['isStock'],
            ];
        }

        //@todo apply rate to price
        //$rate = \MPSToolbox\Services\CurrencyService::getInstance()->getRate();

        #--
        return $distributors;
    }

    /**
     * @param $masterDeviceId
     * @return array
     * @todo
     */
    public static function getServices($masterDeviceId)
    {
        $result = [];
        $db = \Zend_Db_Table::getDefaultAdapter();
        $dealerId = DealerEntity::getDealerId();

        /**
        foreach($db->query(
"
  select
    master_device_service.id,
    'Ingram Micro' as supplier,
    ingram_products.vendor_part_number as sku,
    ingram_products.ingram_part_number as part_number,
    ingram_prices.customer_price as price
  from
    master_device_service
    join ingram_products on ingram_products.vendor_part_number =  master_device_service.vpn and ingram_products.ingram_micro_category=1221
      join ingram_prices on ingram_prices.ingram_part_number = ingram_products.ingram_part_number and ingram_prices.dealerId={$dealerId}
  WHERE
    master_device_service.masterDeviceId = ?
",
        [$masterDeviceId])->fetchAll() as $line) {
            $result[] = $line;
        }

        foreach ($db->query(
            "
  select
    master_device_service.id,
    'Synnex' as supplier,
    synnex_products.Manufacturer_Part as sku,
    synnex_products.SYNNEX_SKU as part_number,
    synnex_prices.Contract_Price as price
  from
    master_device_service
    join synnex_products on synnex_products.Manufacturer_Part =  master_device_service.vpn and SYNNEX_CAT_Code like '010%'
      join synnex_prices on synnex_prices.SYNNEX_SKU = synnex_products.SYNNEX_SKU and synnex_prices.dealerId={$dealerId}
  WHERE
    master_device_service.masterDeviceId = ?
",
            [$masterDeviceId])->fetchAll() as $line) {
            $result[] = $line;
        }
        **/

        return $result;
    }

}