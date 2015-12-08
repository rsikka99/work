<?php

use Tangent\Controller\Action;

class Ecommerce_DealerController extends Action
{
    public function indexAction() {
        $this->_pageTitle = ['E-commerce - Dealer Settings'];

        $db = Zend_Db_Table::getDefaultAdapter();
        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();

        if (($this->getRequest()->getMethod()=='POST') || $this->getRequest()->getParam('delete')) {
            if ($this->getRequest()->getParam('section')=='price_levels') {
                $price_level_add = $this->getRequest()->getParam('price_level_add');
                $price_level_edit = $this->getRequest()->getParam('price_level_edit');
                $price_level_delete = $this->getRequest()->getParam('delete');
                if ($price_level_add) {
                    $st = $db->prepare('insert into dealer_price_levels set dealerId=:dealerId, name=:name, margin=:margin');
                    $st->execute(['dealerId'=>$dealerId, 'name'=>$price_level_add['name'], 'margin'=>$price_level_add['margin']]);
                }
                if ($price_level_edit) {
                    $st = $db->prepare('update dealer_price_levels set name=:name, margin=:margin where id=:id');
                    $st->execute($price_level_edit);
                }
                if ($price_level_delete) {
                    $st = $db->prepare('delete from dealer_price_levels where id=:id');
                    $st->execute(['id'=>$price_level_delete]);
                }
            }
            if ($this->getRequest()->getParam('section')=='main') {
                #--
                $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings();
                $settings->shopSettings->emailFromAddress = $this->getRequest()->getParam('emailFromAddress');
                $settings->shopSettings->emailFromName = $this->getRequest()->getParam('emailFromName');
                $settings->shopSettings->supplyNotifySubject = $this->getRequest()->getParam('supplyNotifySubject');
                $settings->shopSettings->supplyNotifyMessage = $this->getRequest()->getParam('supplyNotifyMessage');
                $settings->shopSettings->supplyNotifySubject2 = $this->getRequest()->getParam('supplyNotifySubject2');
                $settings->shopSettings->supplyNotifyMessage2 = $this->getRequest()->getParam('supplyNotifyMessage2');
                $settings->shopSettings->supplyNotifySubject3 = $this->getRequest()->getParam('supplyNotifySubject3');
                $settings->shopSettings->supplyNotifyMessage3 = $this->getRequest()->getParam('supplyNotifyMessage3');
                $settings->shopSettings->thresholdPercent = max(0,intval($this->getRequest()->getParam('thresholdPercent')));
                $settings->shopSettings->thresholdDays = max(1,intval($this->getRequest()->getParam('thresholdDays')));
                $settings->shopSettings->save();
                #--
                $st = $db->query('SELECT * FROM suppliers LEFT JOIN dealer_suppliers ON suppliers.id = dealer_suppliers.supplierId AND dealerId=' . intval($dealerId));
                $suppliers = $st->fetchAll();
                $post = $this->getRequest()->getParam('supplier');
                if (empty($post)) $post = [];
                foreach ($suppliers as $line) {
                    if (!isset($post[$line['id']]['enabled'])) {
                        $db->query('DELETE FROM dealer_suppliers WHERE supplierId=' . $line['id'] . ' AND dealerId=' . intval($dealerId));
                    }
                }
                foreach ($post as $id => $line) {
                    if (isset($line['enabled'])) {
                        $db->query(
                            'REPLACE INTO dealer_suppliers SET supplierId=' . $id . ', `url`=:url,`user`=:user,`pass`=:pass, dealerId=' . intval($dealerId),
                            ['url' => $line['url'], 'user' => $line['user'], 'pass' => $line['pass']]
                        );
                    }
                }
            }
            #--
            $this->_flashMessenger->addMessage(["success" => "Your changes are saved"]);
            $this->redirect('/ecommerce/dealer');
        }

        $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
        $db = Zend_Db_Table::getDefaultAdapter();
        $st = $db->query('select * from suppliers left join dealer_suppliers on suppliers.id = dealer_suppliers.supplierId and dealerId='.intval($dealerId));
        $this->view->distributors = $st->fetchAll();

        $st = $db->query('select id, name, margin, id IN (SELECT priceLevelId FROM clients) as is_used from dealer_price_levels where dealerId='.intval($dealerId).' order by `margin`');
        $this->view->price_levels = $st->fetchAll();
        if (empty($this->view->price_levels)) {
            $db->query("insert into dealer_price_levels set name='Base', margin='30', dealerId=".intval($dealerId));
            $id = $db->lastInsertId();
            $db->query('update clients set priceLevelId='.intval($id).' where dealerId='.intval($dealerId));
            $st = $db->query('select id, name, margin, id IN (SELECT priceLevelId FROM clients) as is_used from dealer_price_levels where dealerId='.intval($dealerId).' order by `margin`');
            $this->view->price_levels = $st->fetchAll();
        }
    }
}
