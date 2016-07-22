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
            header('Location: '.$_SERVER['REQUEST_URL']);
            exit();
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
                  select base_product.id, base_product.userId, base_product.sku, manufacturers.displayname as mfg, base_product.name, dealer_sku.dealerSku, dealer_sku.cost from base_product
                    join base_sku using (id)
                    join manufacturers on base_product.manufacturerId=manufacturers.id
                    left join dealer_sku on dealer_sku.skuId=base_product.id and dealer_sku.dealerId={$dealerId}
                    where categoryId={$id} and (base_product.userId=1 or base_product.userId={$userId})
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
            $products .= '<td>'.$line['cost'].'</td>';
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

        $id = intval($this->getParam('id'));
        $dealerId = intval(\MPSToolbox\Entities\DealerEntity::getDealerId());
        $db = Zend_Db_Table::getDefaultAdapter();

        if ($this->getRequest()->isPost()) {
            $st = $db->prepare('replace into dealer_category set categoryId=?, dealerId=?, name=?, taxable=?, orderBy=?');
            $st->execute([$id, $dealerId, $this->getParam('name'), $this->getParam('taxable'), $this->getParam('orderBy')]);

            $st = $db->prepare('replace into dealer_category_price_level set categoryId=?, priceLevelId=?, margin=?');
            foreach ($this->getParam('margin') as $priceLevelId=>$margin) {
                $st->execute([$id, $priceLevelId, $margin]);
            }
            echo json_encode(['ok'=>true]);
            return;
        }

        $dealer_price_levels = $db->query('select dealer_price_levels.*, dealer_category_price_level.margin as category_margin from dealer_price_levels left join dealer_category_price_level on dealer_category_price_level.categoryId='.$id.' and dealer_price_levels.id=dealer_category_price_level.priceLevelId where dealerId='.$dealerId.' order by margin');
        $margins = '';
        foreach ($dealer_price_levels as $price_line) {
            $margins .= '<tr>';
            $margins .= '<td>'.$price_line['name'].'</td>';
            $margins .= '<td><input required="required" class="form-control" type="number" step="0.1" value="'.($price_line['category_margin']?$price_line['category_margin']:$price_line['margin']).'" name="margin['.$price_line['id'].']"></td>';
            $margins .= '</tr>';
        }

        $editFunction = 'editSku';
        $showAdd = true;
        $products = $this->getProducts($db, $id, $dealerId, $editFunction, $showAdd);

        $category = $db->query('select * from base_category where id='.$id)->fetch(PDO::FETCH_ASSOC);
        $dealer_category = $db->query('select * from dealer_category where dealerId='.$dealerId.' and categoryId='.$id)->fetch(PDO::FETCH_ASSOC);

        $result=[
            'name'=>    isset($dealer_category['name'])     ? $dealer_category['name'] : $category['name'],
            'taxable'=> isset($dealer_category['taxable'])  ? $dealer_category['taxable'] : 1,
            'orderBy'=> isset($dealer_category['orderBy'])  ? $dealer_category['orderBy'] : 1,
            'showProducts' => $editFunction?true:false,
            'showAdd' => $showAdd?true:false,
            'margins'=>$margins,
            'products'=>$products,
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

}
