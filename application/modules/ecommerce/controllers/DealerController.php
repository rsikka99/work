<?php

use Tangent\Controller\Action;

class Ecommerce_DealerController extends Action
{
    public function indexAction() {
        $this->_pageTitle = ['E-commerce - Dealer Settings'];

        if ($this->getRequest()->getMethod()=='POST') {
            #--
            $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings();
            $settings->shopSettings->emailFromAddress = $this->getRequest()->getParam('emailFromAddress');
            $settings->shopSettings->emailFromName = $this->getRequest()->getParam('emailFromName');
            $settings->shopSettings->supplyNotifySubject = $this->getRequest()->getParam('supplyNotifySubject');
            $settings->shopSettings->supplyNotifyMessage = $this->getRequest()->getParam('supplyNotifyMessage');
            $settings->shopSettings->save();
            #--
            $dealerId = \MPSToolbox\Legacy\Entities\DealerEntity::getDealerId();
            $db = Zend_Db_Table::getDefaultAdapter();
            $st = $db->query('select * from suppliers left join dealer_suppliers on suppliers.id = dealer_suppliers.supplierId and dealerId='.intval($dealerId));
            $suppliers = $st->fetchAll();
            $post = $this->getRequest()->getParam('supplier');
            if (empty($post)) $post=[];
            foreach ($suppliers as $line) {
                if (!isset($post[$line['id']]['enabled'])) {
                    $db->query('delete from dealer_suppliers where supplierId='.$line['id'].' and dealerId='.intval($dealerId));
                }
            }
            foreach ($post as $id=>$line) {
                if (isset($line['enabled'])) {
                    $db->query(
                        'replace into dealer_suppliers set supplierId=' . $id . ', `url`=:url,`user`=:user,`pass`=:pass, dealerId=' . intval($dealerId),
                        ['url' => $line['url'], 'user' => $line['user'], 'pass' => $line['pass']]
                    );
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

    }
}
