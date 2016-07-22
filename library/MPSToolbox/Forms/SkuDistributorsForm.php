<?php

namespace MPSToolbox\Forms;

use MPSToolbox\Legacy\Entities\DealerEntity;

class SkuDistributorsForm extends \My_Form_Form {
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
            if ($e) {
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
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select * from ingram_products p join ingram_prices c using (ingram_part_number) where dealerId='.$dealerId.' and skuId=?');
        $st->execute([$skuId]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>'Ingram Micro',
                'sku'=>$line['ingram_part_number'],
                'price'=>$line['customer_price'],
                'stock'=>$line['availability_flag'],
            ];
        }
        #--
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select * from synnex_products p join synnex_prices c using (SYNNEX_SKU) where dealerId='.$dealerId.' and skuId=?');
        $st->execute([$skuId]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>'Synnex',
                'sku'=>$line['SYNNEX_SKU'],
                'price'=>$line['Contract_Price'],
                'stock'=>$line['Qty_on_Hand'],
            ];
        }
        #--
        $rate = \MPSToolbox\Services\CurrencyService::getInstance()->getRate();
        $st = \Zend_Db_Table::getDefaultAdapter()->prepare('select * from techdata_products p join techdata_prices c using (Matnr) where dealerId='.$dealerId.' and skuId=?');
        $st->execute([$skuId]);
        foreach ($st->fetchAll() as $line) {
            $distributors[] = [
                'name'=>'Tech Data',
                'sku'=>$line['Matnr'],
                'price'=>$rate*$line['CustBestPrice'],
                'stock'=>$line['Qty'],
            ];
        }
        #--
        return $distributors;
    }

    public static function getServices($hardwareId)
    {
        $result = [];
        $db = \Zend_Db_Table::getDefaultAdapter();

        $dealerId = DealerEntity::getDealerId();

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

        return $result;
    }
}