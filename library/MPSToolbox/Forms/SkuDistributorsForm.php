<?php

namespace MPSToolbox\Forms;

use MPSToolbox\Legacy\Entities\DealerEntity;

class SkuDistributorsForm extends \My_Form_Form {
    public $skuId;

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/sku-distributors-form.phtml']]]);
    }

    public static function getDistributors($skuId) {
        $distributors=[];
        $dealerId = DealerEntity::getDealerId();
        #--
        if ($skuId) {
            $db = \Zend_Db_Table::getDefaultAdapter();
            $e = $db->query('select * from dealer_sku where skuId='.intval($skuId).' and dealerId='.intval($dealerId))->fetch();
            if ($e && ($e['cost']>0)) {
                $dealer = \MPSToolbox\Legacy\Mappers\DealerMapper::getInstance()->find($dealerId);
                $distributors[] = [
                    'name' => $dealer->dealerName,
                    'sku' => $e['dealerSku'],
                    'price' => $e['cost'],
                    'stock' => '',
                ];
            }
        }

        #--
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select s.name as supplier_name, supplierSku, price, isStock from supplier_product p join suppliers s on p.supplierId=s.id join supplier_price c using (supplierId, supplierSku) where dealerId='.$dealerId.' and baseProductId=?');
        $st->execute([$skuId]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>$line['supplier_name'],
                'sku'=>$line['supplierSku'],
                'price'=>$line['price'],
                'stock'=>$line['isStock']?'<span style="color:#00572d">Yes</span>':'<span style="color:#880000">No</span>',
            ];
        }
        #--
        return $distributors;
    }

    /**
     * @param $hardwareId
     * @return array
     * @todo
     */
    public static function getServices($hardwareId)
    {
        $result = [];
        $db = \Zend_Db_Table::getDefaultAdapter();
        $dealerId = DealerEntity::getDealerId();

        /**

        foreach($db->query(
            "
  select
    ext_hardware_service.id,
    'Ingram Micro' as supplier,
    ingram_products.vendor_part_number as sku,
    ingram_products.ingram_part_number as part_number,
    ingram_prices.customer_price as price
  from
    ext_hardware_service
    join ingram_products on ingram_products.vendor_part_number =  ext_hardware_service.vpn and ingram_products.ingram_micro_category=1221
      join ingram_prices on ingram_prices.ingram_part_number = ingram_products.ingram_part_number and ingram_prices.dealerId={$dealerId}
  WHERE
    ext_hardware_service.hardwareId = ?
",
            [$hardwareId])->fetchAll() as $line) {
            $result[] = $line;
        }

        foreach ($db->query(
            "
  select
    ext_hardware_service.id,
    'Synnex' as supplier,
    synnex_products.Manufacturer_Part as sku,
    synnex_products.SYNNEX_SKU as part_number,
    synnex_prices.Contract_Price as price
  from
    ext_hardware_service
    join synnex_products on synnex_products.Manufacturer_Part =  ext_hardware_service.vpn and SYNNEX_CAT_Code like '010%'
      join synnex_prices on synnex_prices.SYNNEX_SKU = synnex_products.SYNNEX_SKU and synnex_prices.dealerId={$dealerId}
  WHERE
    ext_hardware_service.hardwareId = ?
",
            [$hardwareId])->fetchAll() as $line) {
            $result[] = $line;
        }
        **/

        return $result;
    }
}