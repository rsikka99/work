<?php

use Tangent\Controller\Action;

class Ecommerce_CatalogController extends Action {

    public function indexAction() {
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $db = Zend_Db_Table::getDefaultAdapter();

        if ($this->getRequest()->isPost()) {
            $add = intval($this->getParam('add'));
            if ($add) {
                $db->query("replace into dealer_category set categoryId={$add}, dealerId={$dealerId}");
            }
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender(true);
            $this->redirect($_SERVER['REQUEST_URI']);
            return;
        }

        $this->_pageTitle = ['E-commerce - Catalog'];
        $this->view->addCategory = '<option></option>';
        $arr = $db->query('select base_category.*, dealer_category.categoryId from base_category left join dealer_category on base_category.id=dealer_category.categoryId and dealerId='.$dealerId.' order by base_category.name')->fetchAll();
        foreach ($arr as $line) {
            if (empty($line['parent'])) {
                foreach ($arr as $line2) {
                    if (($line['id']==$line2['parent']) && empty($line['categoryId'])) {
                        $this->view->addCategory .= '<option value="'.$line2['id'].'">'.$line['name'].' - '.$line2['name'].'</option>';
                    }
                }
            }
        }
    }

    public function categoriesAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $db = Zend_Db_Table::getDefaultAdapter();
        $arr = $db->query('select base_category.*, dealer_category.categoryId, dealer_category.name as dealerName from base_category left join dealer_category on base_category.id=dealer_category.categoryId and dealerId='.$dealerId.' order by dealer_category.orderBy, base_category.name')->fetchAll();

        $result = [];
        foreach($arr as $line) {
            if (empty($line['parent'])) {
                if ($line['dealerName']) $line['name'] = $line['dealerName'];
                $node = ['text' => $line['name'], 'selectable' => true, 'href' => $line['id'], 'state'=>['expanded'=>false]]; //, 'icon'=>'glyphicon glyphicon-folder-close', 'selectedIcon'=>'glyphicon glyphicon-folder-open'
                $children = [];
                foreach($arr as $line2) {
                    if ($line2['parent']==$line['id']) {
                        $visible = false;
                        if (\MPSToolbox\Legacy\Services\NavigationService::$userId==1) $visible=true;
                        if ($line2['categoryId']) $visible = true;
                        if ($line2['id']==9053) $visible = true;
                        if ($line2['id']==9058) $visible = true;
                        if ($line2['id']==9059) $visible = true;
                        if ($line2['id']==90881) $visible = true;
                        if ($line2['id']==90882) $visible = true;
                        if ($visible) {
                            if ($line2['dealerName']) $line2['name'] = $line2['dealerName'];
                            $children[] = ['text' => $line2['name'], 'selectable' => true, 'href' => $line2['id']]; //'icon' => $icon, 'state' => $state,
                        }
                    }
                }
                if (!empty($children)) {
                    $node['nodes'] = $children;
                    $result[] = $node;
                }
            }
        }
        echo json_encode($result);
    }

    protected function getProducts($db, $id, $dealerId, &$editFunction, &$showAdd) {
        $userId = \MPSToolbox\Legacy\Services\NavigationService::$userId;
        $arr=[];
        switch ($id) {
            case 9053 : { //inkjet printers
                $arr = $db->query("
                  select base_product.id, base_product.userId, ifnull(base_product.sku, devices.oemSku) as sku, manufacturers.displayname as mfg, base_product.name, devices.dealerSku, devices.cost from base_product
                    join base_printing_device using (id)
                    join base_printer using (id)
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    join devices on base_product.id = devices.masterDeviceId and devices.dealerId={$dealerId}
                  where tech='Ink'
                ")->fetchAll();
                $editFunction = 'editPrinter';
                $showAdd = false;
                break;
            }
            case 9058 : { // monochrome laser
                $arr = $db->query("
                  select base_product.id, base_product.userId, ifnull(base_product.sku, devices.oemSku) as sku, manufacturers.displayname as mfg, base_product.name, devices.dealerSku, devices.cost from base_product
                    join base_printing_device using (id)
                    join base_printer using (id)
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    join devices on base_product.id = devices.masterDeviceId and devices.dealerId={$dealerId}
                  where (tech is null or tech<>'Ink') and tonerConfigId=1
                ")->fetchAll();
                $editFunction = 'editPrinter';
                $showAdd = false;
                break;
            }
            case 9059 : { // color laser
                $arr = $db->query("
                  select base_product.id, base_product.userId, ifnull(base_product.sku, devices.oemSku) as sku, manufacturers.displayname as mfg, base_product.name, devices.dealerSku, devices.cost from base_product
                    join base_printing_device using (id)
                    join base_printer using (id)
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    join devices on base_product.id = devices.masterDeviceId and devices.dealerId={$dealerId}
                  where (tech is null or tech<>'Ink') and tonerConfigId>1
                ")->fetchAll();
                $editFunction = 'editPrinter';
                $showAdd = false;
                break;
            }
            case 90881 :  // oem supplies
            case 90882 : { // compatible supplies
                $editFunction = null;
                break;
            }
            default : {
                $arr = $db->query("
                  select base_product.id, base_product.userId, base_product.sku, manufacturers.displayname as mfg, base_product.name, dealer_sku.dealerSku, dealer_sku.cost, pp.price as supplier_cost
                    from base_product
                    join base_sku using (id)
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    join dealer_sku on dealer_sku.skuId=base_product.id and dealer_sku.dealerId={$dealerId}
                    left join supplier_product_price pp on base_product.id=pp.baseProductId and pp.dealerId={$dealerId} and pp.price=(select min(spp.price) from supplier_product_price spp where spp.baseProductId=pp.baseProductId and spp.dealerId={$dealerId})
                    where base_product.categoryId={$id}
                    group by base_product.id
                ")->fetchAll();
                $editFunction = 'editSku';
                $showAdd = true;
            }
        }

        $products='';
        if ($editFunction) foreach ($arr as $line) {
            $products .= '<tr>';
            $products .= '<td>'.$line['sku'].'</td>';
            $products .= '<td>'.$line['mfg'].'</td>';
            $products .= '<td>'.$line['name'].'</td>';
            $products .= '<td>'.$line['dealerSku'].'</td>';
            $products .= '<td class="text-right">$'.($line['cost']>0?$line['cost']:number_format($line['supplier_cost'],2)).'</td>';
            $products .= '<td>';
            $products .= '<a href="#" onclick="'.$editFunction.'('.$line['id'].'); return false;"><i class="fa fa-pencil-square-o"></i></a>';
            if ($showAdd && (($userId==1) || ($userId=$line['userId']))) $products .= '&nbsp;&nbsp;&nbsp;&nbsp;<a href="#" onclick="deleteSku('.$line['id'].'); return false;"><i class="text-danger fa fa-remove"></i></a>';
            $products.= '</td>';
            $products .= '</tr>';
        }
        return $products;
    }

    public function categoryAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $categoryId = intval($this->getParam('id'));
        $dealerId = intval(\MPSToolbox\Entities\DealerEntity::getDealerId());
        $db = Zend_Db_Table::getDefaultAdapter();

        $service = new \MPSToolbox\Services\PriceLevelService();

        if ($this->getRequest()->isPost()) {
            $st = $db->prepare('replace into dealer_category set categoryId=?, dealerId=?, name=?, taxable=?, orderBy=?');
            $st->execute([$categoryId, $dealerId, $this->getParam('name'), $this->getParam('taxable'), $this->getParam('orderBy')]);

            $manufacturerId = $this->getParam('manufacturer');
            $type = $this->getParam('type');
            $useDefaultMargins = $this->getParam('useDefaultMargins');

            if (!$manufacturerId) $manufacturerId=null;
            if (!$type) $type=null;

            if ($manufacturerId || $type) {
                if ($useDefaultMargins) {
                    $service->deleteCategoryPriceLevel($dealerId, $categoryId, $manufacturerId, $type);
                    echo json_encode(['ok'=>true]);
                    return;
                }
            }

            $service->replaceCategoryPriceLevel($dealerId, $categoryId, $manufacturerId, $type, $this->getParam('margin'));
            echo json_encode(['ok'=>true]);
            return;
        }

        $dealer_price_levels = $service->listByDealerAndCategory($dealerId, $categoryId, null, null);
        $margins = $this->marginTable($dealer_price_levels);

        $editFunction = 'editSku';
        $showAdd = true;
        $products = $this->getProducts($db, $categoryId, $dealerId, $editFunction, $showAdd);

        $category = $db->query('select * from base_category where id='.$categoryId)->fetch(PDO::FETCH_ASSOC);
        $dealer_category = $db->query('select * from dealer_category where dealerId='.$dealerId.' and categoryId='.$categoryId)->fetch(PDO::FETCH_ASSOC);

        $mfgSelect = '<option value=""> - default - </option>';
        $typeSelect = '<option value=""> - default - </option>';

        foreach ($db->query('select manufacturerId, displayname from base_product join manufacturers on base_product.manufacturerId=manufacturers.id where categoryId='.$categoryId.' group by manufacturerId order by displayname') as $line) {
            $mfgSelect.='<option value="'.$line['manufacturerId'].'">'.$line['displayname'].'</option>';
        }

        switch ($categoryId) {
            case 90881 :  // oem supplies
            case 90882 : {
                foreach ($db->query('select distinct(type) as t from base_printer_consumable order by t') as $line) {
                    if ($line['t']) {
                        $typeSelect.='<option value="'.$line['t'].'">'.$line['t'].'</option>';
                    }
                }
                break;
            }
        }

        $result=[
            'name'=>    isset($dealer_category['name'])     ? $dealer_category['name'] : $category['name'],
            'taxable'=> isset($dealer_category['taxable'])  ? $dealer_category['taxable'] : 1,
            'orderBy'=> isset($dealer_category['orderBy'])  ? $dealer_category['orderBy'] : 1,
            'showProducts' => $editFunction?true:false,
            'showAdd' => $showAdd?true:false,
            'margins'=>$margins,
            'products'=>$products,
            'mfgSelect'=>$mfgSelect,
            'typeSelect'=>$typeSelect,
        ];

        echo json_encode($result);
    }

    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);

        $id = $this->getParam('id');
        $category = $this->getParam('category');
        $db = Zend_Db_Table::getDefaultAdapter();
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        $userId = \MPSToolbox\Legacy\Services\NavigationService::$userId;
        $sql= "delete from base_product where categoryId=".intval($category)." and base_type='sku' ".($userId!=1?' and userId='.intval($userId):'')." and id=".intval($id);
        $db->query($sql);
        echo $this->getProducts($db, $category, $dealerId, $ef, $sa);
    }

    public function reloadAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $category = $this->getParam('category');
        $db = Zend_Db_Table::getDefaultAdapter();
        $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
        echo $this->getProducts($db, $category, $dealerId, $ef, $sa);
    }

    public function searchSkuAction() {
        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $q = "%{$this->getParam('q')}%";
        $db = Zend_Db_Table::getDefaultAdapter();
        $st = $db->prepare("select baseProductId, concat(supplierId,';',supplierSku) as id, vpn, description, upc, price from supplier_product join supplier_price using (supplierId, supplierSku) where dealerId=? and (vpn like ? or description like ?) group by vpn");
        $st->execute([$dealerId, $q, $q]);
        echo json_encode($st->fetchAll());
    }

    private function marginTable($dealer_price_levels, $ext_price_levels=null) {
        $margins = '';
        foreach ($dealer_price_levels as $i=>$price_line) {
            $defaultValue = $actualValue = $price_line['category_margin']?$price_line['category_margin']:$price_line['margin'];
            foreach ($ext_price_levels as $e) {
                if ($e['id']==$price_line['id']) {
                    if ($e['category_margin']) {
                        $actualValue = $e['category_margin'];
                    }
                }
            }
            $margins .= '<tr>';
            $margins .= '<td>'.$price_line['name'].'</td>';
            $margins .= '<td><input data-default="'.$defaultValue.'" data-actual="'.$actualValue.'" required="required" class="form-control" type="number" step="0.1" value="'.$actualValue.'" name="margin['.$price_line['id'].']"></td>';
            $margins .= '</tr>';
        }
        return $margins;
    }

    public function mfgOrTypeAction() {
        $mfg = $this->getParam('mfg');
        $type = $this->getParam('type');

        if (!$mfg) $mfg = null;
        if (!$type) $type = null;

        $categoryId = intval($this->getParam('id'));
        $dealerId = intval(\MPSToolbox\Entities\DealerEntity::getDealerId());

        $service = new \MPSToolbox\Services\PriceLevelService();

        $dealer_price_levels = $service->listByDealerAndCategory($dealerId, $categoryId, null, null);
        $ext_price_levels = $service->listByDealerAndCategory($dealerId, $categoryId, $mfg, $type);

        $margins = $this->marginTable($dealer_price_levels, $ext_price_levels);
        $useDefaultMargins = true;
        foreach ($ext_price_levels as $line) if ($line['category_margin']) $useDefaultMargins=false;

        $result = [
            'useDefaultMargins'=>$useDefaultMargins,
            'margins'=>$margins,
        ];
        $this->sendJson($result);
    }

}
