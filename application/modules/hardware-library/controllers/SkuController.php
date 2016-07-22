<?php

use Tangent\Controller\Action;

class HardwareLibrary_SkuController extends Action {

    /** @var  boolean */
    private $isAdmin;

    /** @var  \MPSToolbox\Legacy\Models\UserModel */
    protected $identity;

    public $skuId;

    public function init ()
    {
        $this->isAdmin = $this->view->IsAllowed(\MPSToolbox\Legacy\Models\Acl\AdminAclModel::RESOURCE_ADMIN_TONER_WILDCARD, \MPSToolbox\Legacy\Models\Acl\AppAclModel::PRIVILEGE_ADMIN);
        $this->identity            = \Zend_Auth::getInstance()->getIdentity();
    }

    /**
     * Displays all devices
     */
    public function indexAction ()
    {
        $this->_pageTitle    = 'SKU';
        $this->view->isAdmin = $this->isAdmin;
    }

    protected function getSku() {
        if (!$this->skuId) return null;
        $db = Zend_Db_Table::getDefaultAdapter();
        return $db->query('select * from base_product join base_sku using (id) where base_product.id='.intval($this->skuId))->fetch();
    }

    protected function getCategory($categoryId) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $result = $db->query('select * from base_category where id='.intval($categoryId))->fetch();
        if ($result && !$result['properties']) {
            if ($result['parent']) {
                $parent = $db->query('SELECT * FROM base_category WHERE id=' . $result['parent'])->fetch();
                $result['properties'] = $parent['properties'];
            }
        }
        return $result;
    }

    protected function saveSku($sku, $quote=null) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $set = "base_type='sku', userId=:userId, manufacturerId=:manufacturerId, sku=:sku, name=:name, isSystemProduct=:isSystemProduct, imageFile=:imageFile, imageUrl=:imageUrl, weight=:weight, UPC=:UPC, categoryId=:categoryId";
        $base_product = [
            'userId'=>\MPSToolbox\Legacy\Services\NavigationService::$userId,
            'manufacturerId'=>$sku['manufacturerId'],
            'sku'=>$sku['sku'],
            'name'=>$sku['name'],
            'isSystemProduct'=>$sku['isSystemProduct'],
            'imageFile'=>$sku['imageFile'],
            'imageUrl'=>$sku['imageUrl'],
            'weight'=>$sku['weight'],
            'UPC'=>$sku['UPC'],
            'categoryId'=>$sku['categoryId'],
        ];
        if ($sku['id']) {
            $base_product['id']=$sku['id'];
            $st = $db->prepare('update base_product set '.$set.' where id=:id');
        } else {
            $st = $db->prepare('insert into base_product set '.$set);
        }
        $st->execute($base_product);
        if (!$sku['id']) {
            $sku['id'] = $db->lastInsertId();
        }

        #--

        if (!empty($sku['sku']) && !empty($sku['manufacturerId'])) {
            $or=[];
            foreach ($db->query('select name from techdata_manufacturer where manufacturerId='.intval($sku['manufacturerId']))->fetchAll() as $line) $or[]="Manufacturer like '{$line['name']}'";
            if (!empty($or)) {
                $st = $db->prepare("UPDATE techdata_products SET skuId=? WHERE (" . implode(' or ', $or) . ") AND ManufPartNo LIKE ?");
                $st->execute([$sku['id'], $sku['sku'] . '%']);
                if ($st->rowCount()>0) {
                    $line = $db->query('select * from techdata_products where skuId='.intval($sku['id']))->fetch();
                    if ($line) {
                        $st = $db->prepare('UPDATE base_product SET weight=? WHERE id=?');
                        $st->execute([0.453592 * $line['Weight'], $sku['id']]);
                    }
                }
            }

            $or=[];
            foreach ($db->query('select name from ingram_manufacturer where manufacturerId='.intval($sku['manufacturerId']))->fetchAll() as $line) $or[]="vendor_name like '{$line['name']}'";
            if (!empty($or)) {
                $st = $db->prepare("UPDATE ingram_products SET skuId=? WHERE (" . implode(' or ', $or) . ") AND vendor_part_number LIKE ?");
                $st->execute([$sku['id'], $sku['sku'] . '%']);
                if ($st->rowCount()>0) {
                    $line = $db->query('select * from ingram_products where skuId='.intval($sku['id']))->fetch();
                    if ($line) {
                        $st = $db->prepare('UPDATE base_product SET UPC=?, weight=? WHERE id=?');
                        $st->execute([$line['upc_code'], 0.453592 * $line['weight'], $sku['id']]);
                    }
                }
            }

            $or=[];
            foreach ($db->query('select name from synnex_manufacturer where manufacturerId='.intval($sku['manufacturerId']))->fetchAll() as $line) $or[]="Manufacturer_name like '{$line['name']}'";
            if (!empty($or)) {
                $st = $db->prepare("UPDATE synnex_products SET skuId=? WHERE (" . implode(' or ', $or) . ") AND Manufacturer_part LIKE ?");
                $st->execute([$sku['id'], $sku['sku'] . '%']);
                if ($st->rowCount()>0) {
                    $line = $db->query('select * from synnex_products where skuId='.intval($sku['id']))->fetch();
                    if ($line) {
                        $st = $db->prepare('UPDATE base_product SET UPC=?, weight=? WHERE id=?');
                        $st->execute([$line['UPC_Code'], 0.453592 * $line['Ship_Weight'], $sku['id']]);
                    }
                }
            }
        }

        #--

        $st = $db->prepare('replace into base_sku set id=?, properties=?');
        $st->execute([$sku['id'], $sku['properties']]);

        if ($quote) {
            $dealerId = \MPSToolbox\Entities\DealerEntity::getDealerId();
            $line = $db->query('select * from dealer_sku where skuId='.intval($sku['id']).' and dealerId='.intval($dealerId))->fetch();
            if ($line) {
                $st = $db->prepare('update dealer_sku set dealerSku=?, cost=?, fixedPrice=?, taxable=?, dataSheetUrl=?, reviewsUrl=?, online=?, onlineDescription=? where skuId=? and dealerId=?');
            } else {
                $st = $db->prepare('replace into dealer_sku set dealerSku=?, cost=?, fixedPrice=?, taxable=?, dataSheetUrl=?, reviewsUrl=?, online=?, onlineDescription=?, skuId=?, dealerId=?');
            }
            $st->execute([
                $quote['dealerSku'],
                $quote['cost'],
                $quote['fixedPrice'],
                empty($quote['taxable'])?0:1,
                $quote['dataSheetUrl'],
                $quote['reviewsUrl'],
                $quote['online'],
                $quote['onlineDescription'],
                $sku['id'],
                $dealerId
            ]);
        }
    }

    protected function delete($id) {
        $db = Zend_Db_Table::getDefaultAdapter();
        $db->query("delete from base_product where base_type='sku' and id=".$id);
    }

    /**
    public function deleteServiceAction() {
        $skuId = $this->getParam('skuId');
        $id = $this->getParam('id');
        if ($skuId && $id) {
            $db = \Zend_Db_Table::getDefaultAdapter();
            $db->query('DELETE FROM ext_hardware_service WHERE id=? and skuId=?', [$id, $skuId])->execute();
        }
        $result = HardwareDistributorsForm::getServices($skuId);
        $this->sendJson($result);
    }
    public function addServiceAction() {
        $skuId = $this->getParam('skuId');
        $sku = $this->getParam('sku');
        $db = \Zend_Db_Table::getDefaultAdapter();
        if ($skuId && $sku) {
            $db->prepare('insert into ext_hardware_service set skuId=?, vpn=?')->execute([$skuId, $sku]);
        }
        $result = HardwareDistributorsForm::getServices($skuId);
        $this->sendJson($result);
    }
    **/

    public function loadFormsAction ()
    {
        // image upload
        if (!empty($_FILES) && $this->getParam('id')) {
            $this->skuId = $this->_getParam('id', false);
            $sku = $this->getSku();
            if (!$sku) {
                $this->sendJsonError('not found');
                return;
            }

            $isAllowed = (!$sku->isSystemProduct || $this->isAdmin) ? true : false;
            if (!$isAllowed) {
                $this->sendJsonError('not allowed');
                return;
            }

            $category = $this->getCategory($sku['categoryId']);
            $service = new \MPSToolbox\Services\SkuService(json_decode($category['properties'], true), $sku, $this->identity->dealerId, $isAllowed, $this->isAdmin);
            foreach ($_FILES as $upload) {
                $sku['imageFile'] = $service->uploadImage($upload);
                $this->saveSku($sku);
            }

            $result = array(
                'filename'=>$sku['imageFile']
            );
            $this->sendJson($result);
        }

        $this->_helper->layout()->disableLayout();
        $this->skuId = $this->_getParam('skuId', false);
        $sku = $this->getSku();
        $isAllowed = (!$sku->isSystemProduct || $this->isAdmin) ? true : false;

        $category = $this->getCategory($this->getParam('categoryId'));
        $p = json_decode($category['properties'], true);
        $service = new \MPSToolbox\Services\SkuService($p, $sku, $this->identity->dealerId, $isAllowed, $this->isAdmin);

        $forms = $service->getForms();
        foreach ($forms as $formName => $form) {
            $this->view->$formName = $form;
        }

        $this->view->categoryId = $this->getParam('categoryId');
        $this->view->sku = $sku;
        $this->view->isAllowed = $isAllowed;
        $this->view->manufacturers = \MPSToolbox\Legacy\Modules\ProposalGenerator\Mappers\ManufacturerMapper::getInstance()->fetchAllAvailableManufacturers();
        $this->view->skuId = $this->skuId;
    }

    public function updateAction ()
    {
        if ($this->_request->isPost())
        {
            $postData = $this->_request->getPost();

            $this->skuId = $this->getParam('skuId', false);
            $sku = $this->skuId ? $this->getSku() : [];
            $sku['categoryId']            = $this->getParam('categoryId', false);
            $sku['name']            = $this->getParam('modelName', false);
            $sku['manufacturerId']  = $this->getParam('manufacturerId', false);
            $sku['userId'] = \MPSToolbox\Legacy\Services\NavigationService::$userId;
            $sku['sku']             = $this->getParam('sku', false);
            $sku['isSystemProduct'] = ($sku['userId']==1) ? 1 : 0;
            $sku['weight'] = $this->getParam('weight', false);
            $sku['UPC'] = $this->getParam('UPC', false);

            $category = $this->getCategory($sku['categoryId']);
            $isAllowed                 = ((!$sku->id || !$sku->isSystemDevice || $this->isAdmin) ? true : false);
            $service = new \MPSToolbox\Services\SkuService(json_decode($category['properties'], true), $sku, $this->identity->dealerId, $isAllowed, $this->isAdmin);

            $forms = $service->getForms();
            $modelAndManufacturerErrors = [];
            $formErrors                 = null;

            // Validate model name and manufacturer
            if ($sku['manufacturerId'] <= 0) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['manufacturerId'] = "Please select a valid manufacturer";
            if (empty($sku['name'])) $modelAndManufacturerErrors['modelAndManufacturer']['errorMessages']['modelName'] = "Please enter a model name";

            foreach ($postData as $key => $form) parse_str($postData[$key], $postData[$key]);

            $formErrors = [];
            $validData  = [];

            foreach ($forms as $formName => $form)
            {
                $response = $service->validateData($form, $postData[$formName], $formName);
                if (isset($response['errorMessages'])) $formErrors[$formName] = $response;
                else $validData[$formName] = $response;
            }

            /**
             * Check to see if we had errors. If not lets save!
             */
            if ($formErrors || count($modelAndManufacturerErrors) > 0)
            {
                $this->sendJsonError(array_merge($formErrors, $modelAndManufacturerErrors));
            }
            else
            {
                $sku['properties'] = json_encode($validData['skuAttributes']);

                try
                {
                    if ($validData['hardwareImage']['imageUrl'] && (0!==strcmp($validData['skuImage']['imageUrl'], $sku->imageUrl))) {
                        $service->downloadImageFromImageUrl($validData['skuImage']['imageUrl']);
                    } else {
                        $validData['hardwareImage']['imageUrl'] = $sku->imageUrl;
                    }
                    $this->saveSku($sku, $validData['skuQuote']);

                    $this->sendJson([
                            "skuId" => $sku->id,
                            "message" => "Successfully updated sku",
                            'imageFile' => $sku->imageFile]
                    );
                }
                catch (\Exception $e)
                {
                    \Tangent\Logger\Logger::logException($e);
                    $this->sendJsonError($e->getMessage());
                }
            }
        }

        $this->sendJsonError('This method only accepts POST');
    }
    public function deleteAction ()
    {
        if ($this->_request->isPost()) {
            $this->skuId = intval($this->getRequest()->getParam('skuId'));
            if ($this->isAdmin && $this->skuId) {
                $this->delete($this->skuId);
            }
            $this->sendJson(['ok']);
        }
        $this->sendJsonError('This method only accepts POST');
    }

    /**
    public function rentCalcAction() {
        $skuId = $this->getRequest()->getParam('id');
        $dealerId = DealerEntity::getDealerId();
        $e = ExtDealerHardwareEntity::find(['hardware'=>$skuId, 'dealer'=>$dealerId]);

        $services = HardwareDistributorsForm::getServices($skuId);

        $this->sendJson([
            'hardware'=>empty($e)?0:$e->getCost(),
            'service'=>empty($services)?0:$services[0]['price'],
        ]);
    }
    **/
}
