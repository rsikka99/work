<?php

use \MPSToolbox\Entities\RmsUpdateEntity;
use \MPSToolbox\Entities\TonerColorEntity;

class Default_CronController extends \Tangent\Controller\Action {

    public function rmsUpdateAction() {
        set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $onlyClientId = $this->getParam('client');

        $service = new \MPSToolbox\Services\RmsUpdateService();

        $clients = $service->getRmsClients();

        foreach ($clients as $client) {
            if (empty($client['deviceGroup'])) continue;
            if ($onlyClientId && ($onlyClientId!=$client['clientId'])) continue;
            if (!$onlyClientId && ($client['monitoringEnabled']==0)) continue;

            echo "updating client: {$client['clientId']}<br>\n";
            if (preg_match('#^\w+-\w+-\w+-\w+-\w+$#', $client['deviceGroup'])) {
                $devices = $service->update($client['clientId'], new \MPSToolbox\Api\PrintFleet($client['rmsUri']), $client['deviceGroup']);
                $settings = \MPSToolbox\Settings\Entities\DealerSettingsEntity::getDealerSettings($client['dealerId']);
                $service->checkDevices($devices, $client, $settings->shopSettings);
            }
        }
    }

    public function distributorsAction() {
        set_time_limit(0);
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();

        $service = new \MPSToolbox\Services\DistributorUpdateService();

        $dealerSuppliers = $service->getDealerSuppliers();
        $requestedDealer = $this->getRequest()->getParam('dealerId');
        foreach ($dealerSuppliers as $dealerSupplier) {
            if ($requestedDealer && ($requestedDealer!=$dealerSupplier['dealerId'])) continue;
            $service->updatePrices($dealerSupplier);
        }

        $db = Zend_Db_Table::getDefaultAdapter();
        $arr = $db->query("
SELECT dealers.id FROM dealers join `dealer_settings` on dealers.id=dealer_settings.dealerId join shop_settings on dealer_settings.`shopSettingsId`=shop_settings.id where shop_settings.shopifyName<>''
        ")->fetchAll();
        foreach ($arr as $line) {
            file_get_contents('http://proxy.mpstoolbox.com/shopify/dist_update.php?dealerId='.$line['id'].'&origin='.$_SERVER['HTTP_HOST']);
        }
    }

    public function incompleteAction() {
        $this->_helper->viewRenderer->setNoRender(true);
        $this->_helper->layout->disableLayout();
        #--
        $db = Zend_Db_Table::getDefaultAdapter();
        $arr = $db->query('
select mfg.fullName, count(*) as c from master_devices m join manufacturers mfg on m.manufacturerId=mfg.id where m.id not in (
select master_device_id
  from device_toners dt
    join master_devices msub on dt.master_device_id=msub.id
    join toners t on dt.toner_id=t.id and t.manufacturerId = msub.manufacturerId
)
or imageUrl is null
or (isA3=0 and isAccessCard=0 and isADF=0 and isBinding=0 and isCapableOfReportingTonerLevels=0 and isDuplex=0 and isFax=0 and isPIN=0 and isSmartphone=0 and isStapling=0 and isTouchscreen=0 and isUSB=0 and isWalkup=0 and isWired=0 and isWireless=0)
group by manufacturerId
order by fullName')->fetchAll();

        if (!empty($arr)) {

            $total=0;
            foreach ($arr as $line) $total += $line['c'];

            $html =
"
<p>Hello,</p>
<p>There are currently {$c} incomplete printer models.</p>
<p>Here is the break down per manufacturer:</p>
<table>
";
            foreach ($arr as $line) {
                $html .= '<tr><td>'.$line['fullName'].'</td><td>'.$line['c'].'</td></tr>';
            }

            $html .= '</table>';
            $html .= '<p>Have a nice day!<br>MPS Toolbox</p>';

            $email = new \Zend_Mail();
            $email->setFrom('it@tangentmtw.com');
            $email->addTo('root@tangentmtw.com');
            $email->setSubject('Printer Model Notification');
            $email->setBodyHtml($html);
            $email->setBodyText(strip_tags($html));
            try {
                $email->send();
            } catch (\Exception $ex) {
                \Tangent\Logger\Logger::error($ex->getMessage(), $ex);
            }

        }

    }

}

