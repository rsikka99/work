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

}