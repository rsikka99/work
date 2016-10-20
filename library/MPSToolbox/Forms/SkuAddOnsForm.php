<?php

namespace MPSToolbox\Forms;

use MPSToolbox\Legacy\Entities\DealerEntity;

class SkuAddOnsForm extends \My_Form_Form {
    public $skuId;

    public function loadDefaultDecorators ()
    {
        $this->setDecorators([['ViewScript', ['viewScript' => 'forms/hardware-library/sku-addons-form.phtml']]]);
    }

    public static function getAddOns($skuId) {
        $result=[];
        $dealerId = DealerEntity::getDealerId();
        if ($skuId) {
            $db = \Zend_Db_Table::getDefaultAdapter();
            $sql="
          select base_product.id, base_product.sku, manufacturers.displayname as mfg, base_product.name, dealer_sku.cost, pp.price as supplier_cost
                    from base_product
                    join base_sku using (id)
                    join dealer_sku_addon on base_product.id = dealer_sku_addon.addOnId
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    left join dealer_sku on dealer_sku.skuId=base_product.id and dealer_sku.dealerId={$dealerId}
                    left join supplier_product_price pp on base_product.id=pp.baseProductId and pp.dealerId={$dealerId} and pp.price=(select min(spp.price) from supplier_product_price spp where spp.baseProductId=pp.baseProductId and spp.dealerId={$dealerId})
                    where dealer_sku_addon.baseProductId={$skuId}
                    group by base_product.id
            ";
            //echo $sql;
            $result = $db->query($sql)->fetchAll();

            foreach ($result as $i=>$line) {
                $result[$i]['cost'] = '$'. ($result[$i]['cost']>0 ? $result[$i]['cost'] : number_format($result[$i]['supplier_cost'],2));
            }
        }
        return $result;
    }

}