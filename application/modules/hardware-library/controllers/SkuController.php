<?php

use Tangent\Controller\Action;

class HardwareLibrary_SkuController extends Action {

    /** @var  boolean */
    private $isAdmin;

    /** @var  \MPSToolbox\Legacy\Models\UserModel */
    protected $identity;

    public $skuId;
    public $fromSupplier;

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
        $db = Zend_Db_Table::getDefaultAdapter();
        if (!$this->skuId) {
            if ($this->fromSupplier) {
                $pair=explode(';', $this->fromSupplier,2);
                if (count($pair)==2) {
                    $st = $db->prepare('select * from supplier_product join supplier_price using (supplierId, supplierSku) where supplierId=? and supplierSku=?');
                    $st->execute($pair);
                    $line = $st->fetch(PDO::FETCH_ASSOC);
                    if ($line) {
                        return [
                            'manufacturerId'=>$line['manufacturerId'],
                            'weight'=>$line['weight'],
                            'name'=>$line['description'],
                            'sku'=>$line['vpn'],
                            'UPC'=>$line['upc'],
                        ];
                    }
                }
            }
            return null;
        }
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
            $st = $db->prepare("UPDATE supplier_product SET baseProductId=? WHERE manufacturerId={$sku['manufacturerId']} AND vpn LIKE ?");
            $st->execute([$sku['id'], $sku['sku'] . '%']);
            if ($st->rowCount()>0) {
                $line = $db->query('select * from supplier_product where baseProductId='.intval($sku['id']))->fetch();
                if ($line) {
                    $st = $db->prepare('UPDATE base_product SET UPC=?, weight=? WHERE id=?');
                    $st->execute([$line['upc'], $line['weight'], $sku['id']]);
                }
            }
        }

        #--

        $line = $db->query('select * from base_sku where id='.intval($sku['id']))->fetch();
        if ($line) {
            $st = $db->prepare('update base_sku SET properties=? where id=?');
            $st->execute([$sku['properties'], $sku['id']]);
        } else {
            $st = $db->prepare('REPLACE INTO base_sku SET id=?, properties=?');
            $st->execute([$sku['id'], $sku['properties']]);
        }

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
        return $sku;
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
        $this->fromSupplier = $this->_getParam('fromSupplier', false);
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
                    /**
                    if ($validData['skuImage']['imageUrl'] && (0!==strcmp($validData['skuImage']['imageUrl'], $sku['imageUrl']))) {
                        $sku['imageUrl'] = $validData['skuImage']['imageUrl'];
                        $sku['imageFile'] = $service->downloadImageFromImageUrl($validData['skuImage']['imageUrl']);
                    } else {
                        $validData['skuImage']['imageUrl'] = $sku['imageUrl'];
                    }
                    **/
                    $sku = $this->saveSku($sku, $validData['skuQuote']);

                    $this->sendJson([
                            "skuId" => $sku['id'],
                            "message" => "Successfully updated sku",
                            //'imageFile' => $sku['imageFile']
                    ]);
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

    public function addImageAction() {
        $baseProductId = $this->getParam('baseProductId');
        $url = $this->getParam('url');
        $result = [];
        if ($url) {
            $i = new \MPSToolbox\Services\ImageService();
            $cloud_url = $i->addImage($baseProductId, $url, \MPSToolbox\Services\ImageService::LOCAL_SKU_DIR, \MPSToolbox\Services\ImageService::TAG_SKU);
            if ($cloud_url) {
                $urls = $i->getImageUrls($baseProductId);
                $tr='';
                foreach ($urls as $id=>$url) {
                    $tr.='<tr>
                        <td><img src="'.$url.'" style="width:150px;max-height:150px">
                        <a href="javascript:;" onclick="deleteImage('.$id.')" style="color:red">delete</a></td>
                    </tr>';
                }
                if (!$tr) $tr='<tr><td>no images</td></tr>';
                $result['tr'] = $tr;
            } else {
                $result['error'] = 'Download from URL failed: '.$i->lastError;
            }
        } else {
            $result['error'] = 'No URL provided';
        }
        $this->sendJson($result);
    }

    public function deleteImageAction() {
        $baseProductId = $this->getParam('baseProductId');
        $id = $this->getParam('id');
        $i = new \MPSToolbox\Services\ImageService();
        $i->deleteImageById($id);
        $urls = $i->getImageUrls($baseProductId);
        $tr='';
        foreach ($urls as $id=>$url) {
            $tr.='<tr>
                        <td><img src="'.$url.'" style="width:150px;max-height:150px">
                        <a href="javascript:;" onclick="deleteImage('.$id.')" style="color:red">delete</a></td>
                    </tr>';
        }
        if (!$tr) $tr='<tr><td>no images</td></tr>';
        $result['tr'] = $tr;
        $this->sendJson($result);
    }

}
